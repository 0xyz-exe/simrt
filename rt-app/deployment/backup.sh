#!/bin/bash

# RT Management System - Backup Script
# This script creates automated backups of database and files

set -e

# Configuration
PROJECT_NAME="rt-system"
PROJECT_PATH="/var/www/$PROJECT_NAME"
BACKUP_PATH="/var/backups/rt-system"
RETENTION_DAYS=30
S3_BUCKET="rt-system-backups"
DB_NAME="rt_production"
DB_USER="rt_user"
DB_PASSWORD="your_secure_password_here"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
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

# Create backup directories
create_backup_dirs() {
    mkdir -p "$BACKUP_PATH"/{database,files,logs}
}

# Database backup
backup_database() {
    log "Creating database backup..."

    local timestamp=$(date +"%Y%m%d_%H%M%S")
    local backup_file="$BACKUP_PATH/database/rt_db_$timestamp.sql"

    # PostgreSQL backup
    PGPASSWORD="$DB_PASSWORD" pg_dump -h localhost -U "$DB_USER" -d "$DB_NAME" > "$backup_file"

    # Compress backup
    gzip "$backup_file"

    log "Database backup created: ${backup_file}.gz"

    # Upload to S3 if configured
    if command -v aws &> /dev/null && [ -n "$S3_BUCKET" ]; then
        aws s3 cp "${backup_file}.gz" "s3://$S3_BUCKET/database/" --storage-class STANDARD_IA
        log "Database backup uploaded to S3"
    fi
}

# Files backup
backup_files() {
    log "Creating files backup..."

    local timestamp=$(date +"%Y%m%d_%H%M%S")
    local backup_file="$BACKUP_PATH/files/rt_files_$timestamp.tar.gz"

    # Create tar archive excluding cache and temporary files
    tar -czf "$backup_file" \
        --exclude="$PROJECT_PATH/storage/framework/cache/*" \
        --exclude="$PROJECT_PATH/storage/framework/sessions/*" \
        --exclude="$PROJECT_PATH/storage/framework/views/*" \
        --exclude="$PROJECT_PATH/storage/logs/*" \
        --exclude="$PROJECT_PATH/vendor" \
        --exclude="$PROJECT_PATH/node_modules" \
        --exclude="$PROJECT_PATH/.git" \
        "$PROJECT_PATH"

    log "Files backup created: $backup_file"

    # Upload to S3 if configured
    if command -v aws &> /dev/null && [ -n "$S3_BUCKET" ]; then
        aws s3 cp "$backup_file" "s3://$S3_BUCKET/files/" --storage-class STANDARD_IA
        log "Files backup uploaded to S3"
    fi
}

# Logs backup
backup_logs() {
    log "Creating logs backup..."

    local timestamp=$(date +"%Y%m%d_%H%M%S")
    local backup_file="$BACKUP_PATH/logs/rt_logs_$timestamp.tar.gz"

    # Backup application and system logs
    tar -czf "$backup_file" \
        "$PROJECT_PATH/storage/logs" \
        /var/log/nginx/rt-system-* \
        /var/log/php*-fpm.log 2>/dev/null || true

    log "Logs backup created: $backup_file"
}

# Cleanup old backups
cleanup_old_backups() {
    log "Cleaning up old backups..."

    # Remove local backups older than retention period
    find "$BACKUP_PATH" -type f -mtime +$RETENTION_DAYS -delete

    # Clean up S3 backups if configured
    if command -v aws &> /dev/null && [ -n "$S3_BUCKET" ]; then
        # Set lifecycle policy for automatic deletion
        aws s3api put-bucket-lifecycle-configuration \
            --bucket "$S3_BUCKET" \
            --lifecycle-configuration file:///tmp/s3-lifecycle.json 2>/dev/null || true
    fi

    log "Old backups cleaned up"
}

# Verify backup integrity
verify_backup() {
    log "Verifying backup integrity..."

    local latest_db_backup=$(ls -t "$BACKUP_PATH/database/"*.gz 2>/dev/null | head -1)
    local latest_files_backup=$(ls -t "$BACKUP_PATH/files/"*.tar.gz 2>/dev/null | head -1)

    # Test database backup
    if [ -n "$latest_db_backup" ]; then
        if gzip -t "$latest_db_backup"; then
            log "Database backup integrity verified"
        else
            error "Database backup is corrupted!"
        fi
    fi

    # Test files backup
    if [ -n "$latest_files_backup" ]; then
        if tar -tzf "$latest_files_backup" >/dev/null; then
            log "Files backup integrity verified"
        else
            error "Files backup is corrupted!"
        fi
    fi
}

# Send notification
send_notification() {
    local status=$1
    local message=$2

    # Email notification
    if command -v mail &> /dev/null; then
        echo "$message" | mail -s "RT System Backup $status" "admin@your-domain.com"
    fi

    # Slack notification (if webhook configured)
    if [ -n "$SLACK_WEBHOOK" ]; then
        curl -X POST -H 'Content-type: application/json' \
            --data "{\"text\":\"RT System Backup $status: $message\"}" \
            "$SLACK_WEBHOOK"
    fi
}

# Main backup function
main() {
    log "Starting RT System backup..."

    # Create S3 lifecycle configuration
    cat > /tmp/s3-lifecycle.json <<EOF
{
    "Rules": [
        {
            "ID": "rt-system-backup-lifecycle",
            "Status": "Enabled",
            "Filter": {"Prefix": ""},
            "Transitions": [
                {
                    "Days": 30,
                    "StorageClass": "GLACIER"
                },
                {
                    "Days": 90,
                    "StorageClass": "DEEP_ARCHIVE"
                }
            ],
            "Expiration": {
                "Days": 365
            }
        }
    ]
}
EOF

    create_backup_dirs

    # Perform backups
    backup_database
    backup_files
    backup_logs

    # Maintenance
    verify_backup
    cleanup_old_backups

    # Calculate backup sizes
    local db_size=$(du -sh "$BACKUP_PATH/database" | cut -f1)
    local files_size=$(du -sh "$BACKUP_PATH/files" | cut -f1)
    local logs_size=$(du -sh "$BACKUP_PATH/logs" | cut -f1)

    local success_message="Backup completed successfully!
Database: $db_size
Files: $files_size
Logs: $logs_size
Total backups: $(find "$BACKUP_PATH" -type f | wc -l) files"

    log "$success_message"
    send_notification "Success" "$success_message"

    # Cleanup temp files
    rm -f /tmp/s3-lifecycle.json
}

# Error handling
trap 'error "Backup failed at line $LINENO"; send_notification "Failed" "Backup failed at line $LINENO"' ERR

# Run main function
main "$@"
