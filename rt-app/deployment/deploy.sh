#!/bin/bash

# RT Management System - Production Deployment Script
# Usage: ./deploy.sh [environment]
# Example: ./deploy.sh production

set -e

# Configuration
PROJECT_NAME="rt-system"
PROJECT_PATH="/var/www/$PROJECT_NAME"
REPO_URL="https://github.com/your-username/rt-management-system.git"
BRANCH=${2:-main}
ENVIRONMENT=${1:-production}
PHP_VERSION="8.3"
WEB_USER="www-data"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
}

# Check if running as root
check_root() {
    if [[ $EUID -eq 0 ]]; then
        error "This script should not be run as root for security reasons"
    fi
}

# Install system dependencies
install_dependencies() {
    log "Installing system dependencies..."

    sudo apt update
    sudo apt install -y \
        nginx \
        php${PHP_VERSION}-fpm \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-common \
        php${PHP_VERSION}-mysql \
        php${PHP_VERSION}-pgsql \
        php${PHP_VERSION}-redis \
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-zip \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-gd \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-bcmath \
        php${PHP_VERSION}-intl \
        postgresql \
        postgresql-contrib \
        redis-server \
        git \
        curl \
        unzip \
        supervisor \
        certbot \
        python3-certbot-nginx

    # Install Composer
    if ! command -v composer &> /dev/null; then
        log "Installing Composer..."
        curl -sS https://getcomposer.org/installer | php
        sudo mv composer.phar /usr/local/bin/composer
        sudo chmod +x /usr/local/bin/composer
    fi

    # Install Node.js and npm (for asset compilation if needed)
    if ! command -v node &> /dev/null; then
        log "Installing Node.js..."
        curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
        sudo apt-get install -y nodejs
    fi
}

# Setup database
setup_database() {
    log "Setting up PostgreSQL database..."

    # Create database and user
    sudo -u postgres psql -c "CREATE DATABASE rt_${ENVIRONMENT};" || true
    sudo -u postgres psql -c "CREATE USER rt_user WITH PASSWORD 'your_secure_password_here';" || true
    sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE rt_${ENVIRONMENT} TO rt_user;" || true
    sudo -u postgres psql -c "ALTER USER rt_user CREATEDB;" || true
}

# Setup Redis
setup_redis() {
    log "Configuring Redis..."

    sudo systemctl enable redis-server
    sudo systemctl start redis-server

    # Configure Redis for production
    sudo sed -i 's/^# maxmemory <bytes>/maxmemory 256mb/' /etc/redis/redis.conf
    sudo sed -i 's/^# maxmemory-policy noeviction/maxmemory-policy allkeys-lru/' /etc/redis/redis.conf
    sudo systemctl restart redis-server
}

# Clone or update repository
deploy_code() {
    log "Deploying application code..."

    if [ ! -d "$PROJECT_PATH" ]; then
        log "Cloning repository..."
        sudo git clone $REPO_URL $PROJECT_PATH
        sudo chown -R $USER:$WEB_USER $PROJECT_PATH
    else
        log "Updating repository..."
        cd $PROJECT_PATH
        git fetch origin
        git checkout $BRANCH
        git pull origin $BRANCH
    fi

    cd $PROJECT_PATH
}

# Install PHP dependencies
install_php_dependencies() {
    log "Installing PHP dependencies..."

    composer install --no-dev --optimize-autoloader --no-interaction
}

# Setup environment
setup_environment() {
    log "Setting up environment configuration..."

    # Copy environment file
    if [ ! -f ".env" ]; then
        cp .env.${ENVIRONMENT} .env
        warning "Please update .env file with your production settings"
    fi

    # Generate application key
    php artisan key:generate --force

    # Clear and cache config
    php artisan config:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    # Run migrations
    php artisan migrate --force

    # Seed database if needed
    if [ "$ENVIRONMENT" = "staging" ]; then
        php artisan db:seed --force
    fi

    # Create storage symlink
    php artisan storage:link
}

# Set permissions
set_permissions() {
    log "Setting file permissions..."

    sudo chown -R $USER:$WEB_USER $PROJECT_PATH
    sudo chmod -R 755 $PROJECT_PATH
    sudo chmod -R 775 $PROJECT_PATH/storage
    sudo chmod -R 775 $PROJECT_PATH/bootstrap/cache
    sudo chmod -R 775 $PROJECT_PATH/public
}

# Configure web server
configure_nginx() {
    log "Configuring Nginx..."

    # Copy Nginx configuration
    sudo cp deployment/nginx/rt-system.conf /etc/nginx/sites-available/$PROJECT_NAME

    # Update domain in config
    sudo sed -i "s/rt-system.your-domain.com/$DOMAIN/g" /etc/nginx/sites-available/$PROJECT_NAME

    # Enable site
    sudo ln -sf /etc/nginx/sites-available/$PROJECT_NAME /etc/nginx/sites-enabled/

    # Remove default site
    sudo rm -f /etc/nginx/sites-enabled/default

    # Test configuration
    sudo nginx -t

    # Reload Nginx
    sudo systemctl reload nginx
}

# Setup SSL certificate
setup_ssl() {
    log "Setting up SSL certificate with Let's Encrypt..."

    if [ -n "$DOMAIN" ]; then
        sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos --email admin@$DOMAIN
    else
        warning "No domain specified, skipping SSL setup"
    fi
}

# Setup process monitoring
setup_supervisor() {
    log "Setting up process monitoring..."

    # Create supervisor config for queue workers
    sudo tee /etc/supervisor/conf.d/rt-system-worker.conf > /dev/null <<EOF
[program:rt-system-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $PROJECT_PATH/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=$WEB_USER
numprocs=2
redirect_stderr=true
stdout_logfile=$PROJECT_PATH/storage/logs/worker.log
stopwaitsecs=3600
EOF

    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start rt-system-worker:*
}

# Setup log rotation
setup_logrotate() {
    log "Setting up log rotation..."

    sudo tee /etc/logrotate.d/rt-system > /dev/null <<EOF
$PROJECT_PATH/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 0644 $WEB_USER $WEB_USER
}

/var/log/nginx/rt-system-*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    sharedscripts
    postrotate
        if [ -f /var/run/nginx.pid ]; then
            kill -USR1 \$(cat /var/run/nginx.pid)
        fi
    endscript
}
EOF
}

# Setup monitoring cron jobs
setup_monitoring() {
    log "Setting up monitoring cron jobs..."

    # Create monitoring script
    sudo tee $PROJECT_PATH/monitoring/health-check.sh > /dev/null <<'EOF'
#!/bin/bash

# Health check script
APP_URL="https://rt-system.your-domain.com"
LOG_FILE="/var/log/rt-system-health.log"
EMAIL="admin@your-domain.com"

# Check if application is responding
if ! curl -f -s "$APP_URL" > /dev/null; then
    echo "$(date): Application is not responding" >> "$LOG_FILE"
    echo "RT System is down" | mail -s "RT System Alert" "$EMAIL"
fi

# Check database connection
cd /var/www/rt-system
if ! php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1; then
    echo "$(date): Database connection failed" >> "$LOG_FILE"
    echo "RT System database connection failed" | mail -s "RT System Database Alert" "$EMAIL"
fi

# Check disk space
DISK_USAGE=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -gt 85 ]; then
    echo "$(date): Disk usage is ${DISK_USAGE}%" >> "$LOG_FILE"
    echo "RT System server disk usage is ${DISK_USAGE}%" | mail -s "RT System Disk Alert" "$EMAIL"
fi
EOF

    sudo chmod +x $PROJECT_PATH/monitoring/health-check.sh

    # Add to crontab
    (crontab -l 2>/dev/null; echo "*/5 * * * * $PROJECT_PATH/monitoring/health-check.sh") | crontab -
}

# Main deployment function
main() {
    log "Starting deployment of RT Management System..."

    # Get domain from user if not set
    if [ -z "$DOMAIN" ]; then
        read -p "Enter your domain name (e.g., rt-system.your-domain.com): " DOMAIN
    fi

    check_root
    install_dependencies
    setup_database
    setup_redis
    deploy_code
    install_php_dependencies
    setup_environment
    set_permissions
    configure_nginx
    setup_ssl
    setup_supervisor
    setup_logrotate
    setup_monitoring

    log "Deployment completed successfully!"
    info "Your RT Management System is now available at: https://$DOMAIN"
    info "Please update your .env file with production settings"
    info "Don't forget to configure your backup strategy"
}

# Run main function
main "$@"
