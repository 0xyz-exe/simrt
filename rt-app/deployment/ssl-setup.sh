#!/bin/bash

# SSL Certificate Automation Script for RT Management System
# This script handles SSL certificate installation and renewal

set -euo pipefail

# Configuration
DOMAIN="${1:-rt-system.your-domain.com}"
EMAIL="${2:-admin@your-domain.com}"
WEBROOT="/var/www/rt-system"
NGINX_CONFIG="/etc/nginx/sites-available/rt-system"
APACHE_CONFIG="/etc/apache2/sites-available/rt-system.conf"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Logging
LOG_FILE="/var/log/ssl-setup.log"
exec 1> >(tee -a "$LOG_FILE")
exec 2> >(tee -a "$LOG_FILE" >&2)

log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}"
    exit 1
}

# Check if running as root
check_root() {
    if [[ $EUID -ne 0 ]]; then
        error "This script must be run as root"
    fi
}

# Detect web server
detect_webserver() {
    if systemctl is-active --quiet nginx; then
        echo "nginx"
    elif systemctl is-active --quiet apache2; then
        echo "apache2"
    else
        error "Neither nginx nor apache2 is running"
    fi
}

# Install Certbot
install_certbot() {
    log "Installing Certbot..."

    if command -v certbot &> /dev/null; then
        log "Certbot already installed"
        return
    fi

    # Update package list
    apt update

    # Install snapd if not present
    if ! command -v snap &> /dev/null; then
        apt install -y snapd
        systemctl enable snapd
        systemctl start snapd
    fi

    # Install certbot via snap
    snap install core; snap refresh core
    snap install --classic certbot

    # Create symlink
    ln -sf /snap/bin/certbot /usr/bin/certbot

    log "Certbot installed successfully"
}

# Configure Nginx for SSL
configure_nginx_ssl() {
    log "Configuring Nginx for SSL..."

    # Backup original config
    cp "$NGINX_CONFIG" "$NGINX_CONFIG.backup.$(date +%Y%m%d_%H%M%S)"

    # Create SSL configuration
    cat > "$NGINX_CONFIG" << EOF
# HTTP redirect to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN www.$DOMAIN;

    # Certbot challenge location
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }

    # Redirect all other requests to HTTPS
    location / {
        return 301 https://\$server_name\$request_uri;
    }
}

# HTTPS server
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name $DOMAIN www.$DOMAIN;

    root $WEBROOT/public;
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/$DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$DOMAIN/privkey.pem;
    ssl_trusted_certificate /etc/letsencrypt/live/$DOMAIN/chain.pem;

    # SSL Security Settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-SHA384;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    ssl_session_tickets off;
    ssl_stapling on;
    ssl_stapling_verify on;

    # Security Headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'self';" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Rate Limiting
    limit_req zone=api burst=20 nodelay;
    limit_req zone=login burst=5 nodelay;

    # PHP Configuration
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;

        # Security
        fastcgi_param HTTPS on;
        fastcgi_param SERVER_PORT 443;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)\$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ ^/(\.env|composer\.(json|lock)|package\.(json|lock)|yarn\.lock|\.git) {
        deny all;
        access_log off;
        log_not_found off;
    }
}
EOF

    log "Nginx SSL configuration created"
}

# Configure Apache for SSL
configure_apache_ssl() {
    log "Configuring Apache for SSL..."

    # Enable required modules
    a2enmod ssl rewrite headers

    # Backup original config
    cp "$APACHE_CONFIG" "$APACHE_CONFIG.backup.$(date +%Y%m%d_%H%M%S)"

    # Create SSL configuration
    cat > "$APACHE_CONFIG" << EOF
# HTTP redirect to HTTPS
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    DocumentRoot $WEBROOT/public

    # Certbot challenge
    Alias /.well-known/acme-challenge/ /var/www/certbot/.well-known/acme-challenge/
    <Directory "/var/www/certbot/.well-known/acme-challenge/">
        Options None
        AllowOverride None
        Require all granted
    </Directory>

    # Redirect to HTTPS
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/\.well-known/acme-challenge/
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
</VirtualHost>

# HTTPS server
<VirtualHost *:443>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    DocumentRoot $WEBROOT/public

    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/$DOMAIN/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/$DOMAIN/privkey.pem

    # SSL Security
    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384
    SSLHonorCipherOrder on
    SSLCompression off
    SSLUseStapling on
    SSLStaplingCache "shmcb:logs/stapling-cache(150000)"

    # Security Headers
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'self';"

    # PHP Configuration
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/php/php8.3-fpm.sock|fcgi://localhost"
    </FilesMatch>

    # Laravel configuration
    <Directory "$WEBROOT/public">
        Options -Indexes
        AllowOverride All
        Require all granted
    </Directory>

    # Deny access to sensitive files
    <Files ".env">
        Require all denied
    </Files>

    <FilesMatch "^(composer\.(json|lock)|package\.(json|lock)|yarn\.lock)$">
        Require all denied
    </FilesMatch>

    # Gzip compression
    LoadModule deflate_module modules/mod_deflate.so
    <Location />
        SetOutputFilter DEFLATE
        SetEnvIfNoCase Request_URI \
            \.(?:gif|jpe?g|png)$ no-gzip dont-vary
        SetEnvIfNoCase Request_URI \
            \.(?:exe|t?gz|zip|bz2|sit|rar)$ no-gzip dont-vary
    </Location>
</VirtualHost>
EOF

    log "Apache SSL configuration created"
}

# Obtain SSL certificate
obtain_certificate() {
    local webserver="$1"

    log "Obtaining SSL certificate for $DOMAIN..."

    # Create webroot directory for challenges
    mkdir -p /var/www/certbot/.well-known/acme-challenge
    chown -R www-data:www-data /var/www/certbot

    # Test configuration first
    if [[ "$webserver" == "nginx" ]]; then
        nginx -t || error "Nginx configuration test failed"
        systemctl reload nginx
    else
        apache2ctl configtest || error "Apache configuration test failed"
        systemctl reload apache2
    fi

    # Obtain certificate
    certbot certonly \
        --webroot \
        --webroot-path=/var/www/certbot \
        --email "$EMAIL" \
        --agree-tos \
        --no-eff-email \
        --domains "$DOMAIN,www.$DOMAIN" \
        --expand \
        --non-interactive || error "Failed to obtain SSL certificate"

    log "SSL certificate obtained successfully"
}

# Configure auto-renewal
setup_auto_renewal() {
    log "Setting up SSL certificate auto-renewal..."

    # Create renewal script
    cat > /usr/local/bin/renew-ssl.sh << 'EOF'
#!/bin/bash

# SSL Certificate Renewal Script

LOG_FILE="/var/log/ssl-renewal.log"
exec 1> >(tee -a "$LOG_FILE")
exec 2> >(tee -a "$LOG_FILE" >&2)

echo "[$(date)] Starting SSL certificate renewal check..."

# Renew certificates
/usr/bin/certbot renew --quiet

# Check if renewal was successful
if [ $? -eq 0 ]; then
    echo "[$(date)] Certificate renewal check completed successfully"

    # Reload web server
    if systemctl is-active --quiet nginx; then
        systemctl reload nginx
        echo "[$(date)] Nginx reloaded"
    elif systemctl is-active --quiet apache2; then
        systemctl reload apache2
        echo "[$(date)] Apache reloaded"
    fi
else
    echo "[$(date)] Certificate renewal failed!"
    exit 1
fi
EOF

    chmod +x /usr/local/bin/renew-ssl.sh

    # Add cron job for auto-renewal (twice daily)
    cat > /etc/cron.d/ssl-renewal << EOF
# SSL Certificate Auto-Renewal
# Runs twice daily at random times to avoid peak hours
SHELL=/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

# Random minute between 0-59, hour 2 and 14
$(shuf -i 0-59 -n 1) 2 * * * root /usr/local/bin/renew-ssl.sh
$(shuf -i 0-59 -n 1) 14 * * * root /usr/local/bin/renew-ssl.sh
EOF

    # Test renewal process
    certbot renew --dry-run || warn "SSL renewal dry-run failed"

    log "SSL auto-renewal configured successfully"
}

# Main execution
main() {
    log "Starting SSL setup for RT Management System"
    log "Domain: $DOMAIN"
    log "Email: $EMAIL"

    # Checks
    check_root

    # Detect web server
    WEBSERVER=$(detect_webserver)
    log "Detected web server: $WEBSERVER"

    # Install Certbot
    install_certbot

    # Configure web server for SSL
    if [[ "$WEBSERVER" == "nginx" ]]; then
        configure_nginx_ssl
    else
        configure_apache_ssl
    fi

    # Obtain certificate
    obtain_certificate "$WEBSERVER"

    # Update configuration with SSL
    if [[ "$WEBSERVER" == "nginx" ]]; then
        systemctl reload nginx
    else
        systemctl reload apache2
    fi

    # Setup auto-renewal
    setup_auto_renewal

    log "SSL setup completed successfully!"
    log "Your site is now accessible at: https://$DOMAIN"
    log "Certificate will auto-renew before expiration"

    # Display certificate info
    certbot certificates
}

# Script usage
usage() {
    echo "Usage: $0 [domain] [email]"
    echo "Example: $0 rt-system.your-domain.com admin@your-domain.com"
    exit 1
}

# Check arguments
if [[ $# -eq 0 ]]; then
    usage
fi

# Run main function
main "$@"
