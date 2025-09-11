#!/bin/bash

# RT Management System - Restore Script
# This script restores backups of database and files

set -e

# Configuration
PROJECT_NAME="rt-system"
PROJECT_PATH="/var/www/$PROJECT_NAME"
BACKUP_PATH="/var/backups/rt-system"
S3_BUCKET="rt-system-backups"
DB_NAME="rt_production"
DB_USER="rt_user"
DB_PASSWORD="your_secure_password_here"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Logging
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

# Show usage
usage() {
    echo "Usage: $0 [OPTIONS]"
    echo "Options:"
    echo "  -d, --database BACKUP_FILE    Restore database from backup file"
    echo "  -f, --files BACKUP_FILE       Restore files from backup file"
    echo "  -l, --list                    List available backups"
    echo "  -s, --s3                      Download backups from S3"
    echo "  -a, --auto                    Auto-restore latest backups"
    echo "  -h, --help                    Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 --list                                    # List available backups"
    echo "  $0 --database rt_db_20231220_120000.sql.gz  # Restore specific database backup"
    echo "  $0 --auto                                    # Restore latest backups"
    exit 1
}

# List available backups
list_backups() {
    log "Available local backups:"
    echo ""

    info "Database backups:"
    if [ -d "$BACKUP_PATH/database" ]; then
        ls -lah "$BACKUP_PATH/database/"*.gz 2>/dev/null | awk '{print $9, $5, $6, $7, $8}' || echo "No database backups found"
    else
        echo "No database backup directory found"
    fi

    echo ""
    info "Files backups:"
    if [ -d "$BACKUP_PATH/files" ]; then
        ls -lah "$BACKUP_PATH/files/"*.tar.gz 2>/dev/null | awk '{print $9, $5, $6, $7, $8}' || echo "No files backups found"
    else
        echo "No files backup directory found"
    fi

    echo ""
    if command -v aws &> /dev/null && [ -n "$S3_BUCKET" ]; then
        info "S3 backups:"
        aws s3 ls "s3://$S3_BUCKET/" --recursive --human-readable 2>/dev/null || echo "Cannot access S3 bucket"
    fi
}

# Download backups from S3
download_from_s3() {
    log "Downloading backups from S3..."

    if ! command -v aws &> /dev/null; then
        error "AWS CLI not installed"
    fi

    if [ -z "$S3_BUCKET" ]; then
        error "S3_BUCKET not configured"
    fi

    mkdir -p "$BACKUP_PATH"/{database,files}

    # Download latest database backup
    local latest_db=$(aws s3 ls "s3://$S3_BUCKET/database/" | sort | tail -1 | awk '{print $4}')
    if [ -n "$latest_db" ]; then
        aws s3 cp "s3://$S3_BUCKET/database/$latest_db" "$BACKUP_PATH/database/"
        log "Downloaded database backup: $latest_db"
    fi

    # Download latest files backup
    local latest_files=$(aws s3 ls "s3://$S3_BUCKET/files/" | sort | tail -1 | awk '{print $4}')
    if [ -n "$latest_files" ]; then
        aws s3 cp "s3://$S3_BUCKET/files/$latest_files" "$BACKUP_PATH/files/"
        log "Downloaded files backup: $latest_files"
    fi
}

# Create maintenance page
create_maintenance_page() {
    log "Enabling maintenance mode..."

    cd "$PROJECT_PATH"
    php artisan down --message="System is being restored from backup" --retry=60
}

# Remove maintenance page
remove_maintenance_page() {
    log "Disabling maintenance mode..."

    cd "$PROJECT_PATH"
    php artisan up
}

# Backup current state before restore
backup_current_state() {
    log "Creating backup of current state..."

    local timestamp=$(date +"%Y%m%d_%H%M%S")

    # Backup current database
    PGPASSWORD="$DB_PASSWORD" pg_dump -h localhost -U "$DB_USER" -d "$DB_NAME" | gzip > "$BACKUP_PATH/database/pre_restore_$timestamp.sql.gz"

    # Backup current files
    tar -czf "$BACKUP_PATH/files/pre_restore_$timestamp.tar.gz" \
        --exclude="$PROJECT_PATH/storage/framework/cache/*" \
        --exclude="$PROJECT_PATH/storage/framework/sessions/*" \
        --exclude="$PROJECT_PATH/storage/framework/views/*" \
        --exclude="$PROJECT_PATH/vendor" \
        --exclude="$PROJECT_PATH/node_modules" \
        --exclude="$PROJECT_PATH/.git" \
        "$PROJECT_PATH"

    log "Current state backed up with timestamp: $timestamp"
}

# Restore database
restore_database() {
    local backup_file=$1

    if [ ! -f "$backup_file" ]; then
        error "Database backup file not found: $backup_file"
    fi

    log "Restoring database from: $backup_file"

    # Verify backup file
    if ! gzip -t "$backup_file"; then
        error "Database backup file is corrupted"
    fi

    # Create a backup of current database
    warning "This will overwrite the current database. Current state will be backed up."
    read -p "Are you sure you want to continue? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        error "Restore cancelled by user"
    fi

    # Drop and recreate database
    sudo -u postgres psql -c "DROP DATABASE IF EXISTS ${DB_NAME}_restore;"
    sudo -u postgres psql -c "CREATE DATABASE ${DB_NAME}_restore OWNER $DB_USER;"

    # Restore backup to temporary database
    gunzip -c "$backup_file" | PGPASSWORD="$DB_PASSWORD" psql -h localhost -U "$DB_USER" -d "${DB_NAME}_restore"

    # Switch databases
    sudo -u postgres psql -c "ALTER DATABASE $DB_NAME RENAME TO ${DB_NAME}_old;"
    sudo -u postgres psql -c "ALTER DATABASE ${DB_NAME}_restore RENAME TO $DB_NAME;"

    log "Database restored successfully"

    # Run migrations to ensure schema is up to date
    cd "$PROJECT_PATH"
    php artisan migrate --force

    # Drop old database after successful restore
    sudo -u postgres psql -c "DROP DATABASE IF EXISTS ${DB_NAME}_old;"
}

# Restore files
restore_files() {
    local backup_file=$1

    if [ ! -f "$backup_file" ]; then
        error "Files backup file not found: $backup_file"
    fi

    log "Restoring files from: $backup_file"

    # Verify backup file
    if ! tar -tzf "$backup_file" >/dev/null; then
        error "Files backup file is corrupted"
    fi

    warning "This will overwrite current application files. Current state will be backed up."
    read -p "Are you sure you want to continue? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        error "Restore cancelled by user"
    fi

    # Create temporary extraction directory
    local temp_dir="/tmp/rt-restore-$(date +%s)"
    mkdir -p "$temp_dir"

    # Extract backup
    tar -xzf "$backup_file" -C "$temp_dir"

    # Find the project directory in backup
    local backup_project_path=$(find "$temp_dir" -name "artisan" -type f | head -1 | xargs dirname)

    if [ -z "$backup_project_path" ]; then
        error "Invalid backup file: Laravel project not found"
    fi

    # Stop services
    sudo systemctl stop nginx
    sudo supervisorctl stop rt-system-worker:*

    # Restore files (excluding sensitive files)
    rsync -av \
        --exclude=".env" \
        --exclude="storage/logs/*" \
        --exclude="vendor/" \
        --exclude="node_modules/" \
        "$backup_project_path/" "$PROJECT_PATH/"

    # Set permissions
    sudo chown -R www-data:www-data "$PROJECT_PATH"
    sudo chmod -R 755 "$PROJECT_PATH"
    sudo chmod -R 775 "$PROJECT_PATH/storage"
    sudo chmod -R 775 "$PROJECT_PATH/bootstrap/cache"

    # Install dependencies
    cd "$PROJECT_PATH"
    composer install --no-dev --optimize-autoloader --no-interaction

    # Clear and rebuild cache
    php artisan config:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    # Start services
    sudo systemctl start nginx
    sudo supervisorctl start rt-system-worker:*

    # Cleanup
    rm -rf "$temp_dir"

    log "Files restored successfully"
}

# Auto restore latest backups
auto_restore() {
    log "Auto-restoring from latest backups..."

    # Find latest backups
    local latest_db=$(ls -t "$BACKUP_PATH/database/"*.gz 2>/dev/null | head -1)
    local latest_files=$(ls -t "$BACKUP_PATH/files/"*.tar.gz 2>/dev/null | head -1)

    if [ -z "$latest_db" ] && [ -z "$latest_files" ]; then
        error "No backups found for auto-restore"
    fi

    # Create maintenance page
    create_maintenance_page

    # Backup current state
    backup_current_state

    # Restore database if available
    if [ -n "$latest_db" ]; then
        restore_database "$latest_db"
    else
        warning "No database backup found for auto-restore"
    fi

    # Restore files if available
    if [ -n "$latest_files" ]; then
        restore_files "$latest_files"
    else
        warning "No files backup found for auto-restore"
    fi

    # Remove maintenance page
    remove_maintenance_page

    log "Auto-restore completed successfully"
}

# Send notification
send_notification() {
    local status=$1
    local message=$2

    # Email notification
    if command -v mail &> /dev/null; then
        echo "$message" | mail -s "RT System Restore $status" "admin@your-domain.com"
    fi
}

# Main function
main() {
    if [ $# -eq 0 ]; then
        usage
    fi

    while [[ $# -gt 0 ]]; do
        case $1 in
            -d|--database)
                if [ -n "$2" ]; then
                    create_maintenance_page
                    backup_current_state
                    restore_database "$BACKUP_PATH/database/$2"
                    remove_maintenance_page
                    send_notification "Success" "Database restored from $2"
                    shift 2
                else
                    error "Database backup file not specified"
                fi
                ;;
            -f|--files)
                if [ -n "$2" ]; then
                    create_maintenance_page
                    backup_current_state
                    restore_files "$BACKUP_PATH/files/$2"
                    remove_maintenance_page
                    send_notification "Success" "Files restored from $2"
                    shift 2
                else
                    error "Files backup file not specified"
                fi
                ;;
            -l|--list)
                list_backups
                shift
                ;;
            -s|--s3)
                download_from_s3
                shift
                ;;
            -a|--auto)
                auto_restore
                send_notification "Success" "Auto-restore completed successfully"
                shift
                ;;
            -h|--help)
                usage
                ;;
            *)
                error "Unknown option: $1"
                ;;
        esac
    done
}

# Error handling
trap 'error "Restore failed at line $LINENO"; remove_maintenance_page; send_notification "Failed" "Restore failed at line $LINENO"' ERR

# Check if running as appropriate user
if [[ $EUID -eq 0 ]]; then
    error "This script should not be run as root for security reasons"
fi

# Run main function
main "$@"
