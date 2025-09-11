#!/bin/bash

# RT Management System - Security Hardening Script
# This script implements security best practices for production deployment

set -e

# Configuration
PROJECT_NAME="rt-system"
PROJECT_PATH="/var/www/$PROJECT_NAME"
FAIL2BAN_CONFIG="/etc/fail2ban/jail.local"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

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

# Install security tools
install_security_tools() {
    log "Installing security tools..."

    sudo apt update
    sudo apt install -y \
        fail2ban \
        ufw \
        rkhunter \
        chkrootkit \
        lynis \
        aide \
        unattended-upgrades \
        apt-listchanges \
        logwatch

    log "Security tools installed"
}

# Configure firewall
configure_firewall() {
    log "Configuring UFW firewall..."

    # Reset firewall
    sudo ufw --force reset

    # Default policies
    sudo ufw default deny incoming
    sudo ufw default allow outgoing

    # Allow SSH (change port if needed)
    sudo ufw allow 22/tcp

    # Allow HTTP and HTTPS
    sudo ufw allow 80/tcp
    sudo ufw allow 443/tcp

    # Allow monitoring (restrict to specific IPs in production)
    sudo ufw allow from 127.0.0.1 to any port 9090  # Prometheus
    sudo ufw allow from 127.0.0.1 to any port 3000  # Grafana
    sudo ufw allow from 127.0.0.1 to any port 8080  # Monitoring dashboard

    # Rate limiting for SSH
    sudo ufw limit ssh

    # Enable firewall
    sudo ufw --force enable

    log "Firewall configured and enabled"
}

# Configure Fail2Ban
configure_fail2ban() {
    log "Configuring Fail2Ban..."

    sudo tee "$FAIL2BAN_CONFIG" > /dev/null <<EOF
[DEFAULT]
# Ban IP for 1 hour after 5 failed attempts within 10 minutes
bantime = 3600
findtime = 600
maxretry = 5
backend = systemd

# Email notifications
destemail = admin@your-domain.com
sendername = Fail2Ban
mta = sendmail
action = %(action_mwl)s

[sshd]
enabled = true
port = ssh
logpath = %(sshd_log)s
maxretry = 3
bantime = 7200

[nginx-http-auth]
enabled = true
filter = nginx-http-auth
logpath = /var/log/nginx/error.log
maxretry = 3

[nginx-noscript]
enabled = true
filter = nginx-noscript
logpath = /var/log/nginx/access.log
maxretry = 6

[nginx-badbots]
enabled = true
filter = nginx-badbots
logpath = /var/log/nginx/access.log
maxretry = 2

[nginx-noproxy]
enabled = true
filter = nginx-noproxy
logpath = /var/log/nginx/access.log
maxretry = 2

[php-url-fopen]
enabled = true
filter = php-url-fopen
logpath = /var/log/nginx/access.log

[rt-system-auth]
enabled = true
filter = rt-system-auth
logpath = $PROJECT_PATH/storage/logs/laravel.log
maxretry = 5
bantime = 1800
EOF

    # Create custom filter for RT System authentication
    sudo tee /etc/fail2ban/filter.d/rt-system-auth.conf > /dev/null <<EOF
[Definition]
failregex = .*authentication\.failed.*ip:\s*<HOST>
            .*Failed login attempt.*from\s*<HOST>
            .*Invalid login.*from\s*<HOST>
ignoreregex =
EOF

    sudo systemctl enable fail2ban
    sudo systemctl restart fail2ban

    log "Fail2Ban configured and started"
}

# Secure SSH configuration
secure_ssh() {
    log "Securing SSH configuration..."

    # Backup original config
    sudo cp /etc/ssh/sshd_config /etc/ssh/sshd_config.bak

    # Apply security settings
    sudo tee -a /etc/ssh/sshd_config > /dev/null <<EOF

# RT System Security Hardening
Protocol 2
PermitRootLogin no
PasswordAuthentication no
PermitEmptyPasswords no
ChallengeResponseAuthentication no
UsePAM yes
X11Forwarding no
PrintMotd no
AcceptEnv LANG LC_*
Subsystem sftp /usr/lib/openssh/sftp-server

# Security settings
MaxAuthTries 3
LoginGraceTime 30
ClientAliveInterval 300
ClientAliveCountMax 2
MaxStartups 2
MaxSessions 2

# Restrict users (uncomment and modify as needed)
# AllowUsers your-username

# Key exchange algorithms
KexAlgorithms curve25519-sha256@libssh.org,diffie-hellman-group16-sha512,diffie-hellman-group18-sha512

# Ciphers
Ciphers chacha20-poly1305@openssh.com,aes256-gcm@openssh.com,aes128-gcm@openssh.com,aes256-ctr,aes192-ctr,aes128-ctr

# MACs
MACs hmac-sha2-256-etm@openssh.com,hmac-sha2-512-etm@openssh.com,hmac-sha2-256,hmac-sha2-512
EOF

    # Test SSH configuration
    sudo sshd -t

    # Restart SSH service
    sudo systemctl restart sshd

    log "SSH configuration secured"
}

# Configure automatic security updates
configure_auto_updates() {
    log "Configuring automatic security updates..."

    # Configure unattended-upgrades
    sudo tee /etc/apt/apt.conf.d/50unattended-upgrades > /dev/null <<EOF
Unattended-Upgrade::Allowed-Origins {
    "\${distro_id}:\${distro_codename}";
    "\${distro_id}:\${distro_codename}-security";
    "\${distro_id}ESMApps:\${distro_codename}-apps-security";
    "\${distro_id}ESM:\${distro_codename}-infra-security";
};

Unattended-Upgrade::Package-Blacklist {
    // "vim";
    // "libc6-dev";
    // "nginx";
    // "postgresql";
};

Unattended-Upgrade::DevRelease "auto";
Unattended-Upgrade::Remove-Unused-Kernel-Packages "true";
Unattended-Upgrade::Remove-New-Unused-Dependencies "true";
Unattended-Upgrade::Remove-Unused-Dependencies "true";
Unattended-Upgrade::Automatic-Reboot "false";
Unattended-Upgrade::Automatic-Reboot-WithUsers "false";
Unattended-Upgrade::Automatic-Reboot-Time "02:00";

Unattended-Upgrade::Mail "admin@your-domain.com";
Unattended-Upgrade::MailOnlyOnError "true";
Unattended-Upgrade::Sender "unattended-upgrades@your-domain.com";
EOF

    # Enable automatic updates
    sudo tee /etc/apt/apt.conf.d/20auto-upgrades > /dev/null <<EOF
APT::Periodic::Update-Package-Lists "1";
APT::Periodic::Download-Upgradeable-Packages "1";
APT::Periodic::AutocleanInterval "7";
APT::Periodic::Unattended-Upgrade "1";
EOF

    sudo systemctl enable unattended-upgrades
    sudo systemctl start unattended-upgrades

    log "Automatic security updates configured"
}

# Harden system kernel parameters
harden_kernel() {
    log "Hardening kernel parameters..."

    sudo tee /etc/sysctl.d/99-rt-system-security.conf > /dev/null <<EOF
# Network security
net.ipv4.ip_forward = 0
net.ipv4.conf.all.send_redirects = 0
net.ipv4.conf.default.send_redirects = 0
net.ipv4.conf.all.accept_redirects = 0
net.ipv4.conf.default.accept_redirects = 0
net.ipv4.conf.all.secure_redirects = 0
net.ipv4.conf.default.secure_redirects = 0
net.ipv6.conf.all.accept_redirects = 0
net.ipv6.conf.default.accept_redirects = 0
net.ipv4.conf.all.log_martians = 1
net.ipv4.conf.default.log_martians = 1
net.ipv4.icmp_echo_ignore_broadcasts = 1
net.ipv4.icmp_ignore_bogus_error_responses = 1
net.ipv4.tcp_syncookies = 1
net.ipv4.conf.all.rp_filter = 1
net.ipv4.conf.default.rp_filter = 1

# Disable IPv6 if not needed
net.ipv6.conf.all.disable_ipv6 = 1
net.ipv6.conf.default.disable_ipv6 = 1
net.ipv6.conf.lo.disable_ipv6 = 1

# Control buffer overflow attacks
kernel.exec-shield = 1
kernel.randomize_va_space = 2

# Hide kernel pointers
kernel.kptr_restrict = 2

# Restrict access to kernel logs
kernel.dmesg_restrict = 1

# Restrict ptrace
kernel.yama.ptrace_scope = 1

# File system security
fs.suid_dumpable = 0
fs.protected_hardlinks = 1
fs.protected_symlinks = 1
EOF

    # Apply kernel parameters
    sudo sysctl --system

    log "Kernel parameters hardened"
}

# Secure file permissions
secure_file_permissions() {
    log "Securing file permissions..."

    # Secure application directory
    sudo chown -R www-data:www-data "$PROJECT_PATH"
    sudo find "$PROJECT_PATH" -type f -exec chmod 644 {} \;
    sudo find "$PROJECT_PATH" -type d -exec chmod 755 {} \;

    # Secure sensitive files
    sudo chmod 600 "$PROJECT_PATH/.env"
    sudo chmod -R 775 "$PROJECT_PATH/storage"
    sudo chmod -R 775 "$PROJECT_PATH/bootstrap/cache"

    # Secure configuration files
    sudo chmod 600 /etc/ssl/private/*
    sudo chmod 644 /etc/ssl/certs/*

    # Remove world-readable permissions from sensitive directories
    sudo chmod o-rwx /var/log/nginx/
    sudo chmod o-rwx /var/log/php*

    log "File permissions secured"
}

# Configure intrusion detection
configure_aide() {
    log "Configuring AIDE intrusion detection..."

    # Initialize AIDE database
    sudo aideinit

    # Move database to expected location
    sudo mv /var/lib/aide/aide.db.new /var/lib/aide/aide.db

    # Create daily check script
    sudo tee /etc/cron.daily/aide > /dev/null <<'EOF'
#!/bin/bash
# AIDE daily check

AIDE_LOG="/var/log/aide/aide.log"
AIDE_EMAIL="admin@your-domain.com"

# Create log directory
mkdir -p /var/log/aide

# Run AIDE check
aide --check > "$AIDE_LOG" 2>&1

# Check for changes
if [ $? -ne 0 ]; then
    # Send alert email
    echo "AIDE detected file system changes. Please review the attached log." | \
        mail -s "AIDE Alert - File System Changes Detected" -A "$AIDE_LOG" "$AIDE_EMAIL"
fi

# Rotate logs
if [ -f "$AIDE_LOG" ] && [ $(stat -c%s "$AIDE_LOG") -gt 10485760 ]; then
    mv "$AIDE_LOG" "$AIDE_LOG.old"
fi
EOF

    sudo chmod +x /etc/cron.daily/aide

    log "AIDE intrusion detection configured"
}

# Configure log monitoring
configure_logwatch() {
    log "Configuring Logwatch..."

    # Configure Logwatch
    sudo tee /etc/logwatch/conf/logwatch.conf > /dev/null <<EOF
LogDir = /var/log
TmpDir = /var/cache/logwatch
MailTo = admin@your-domain.com
MailFrom = logwatch@your-domain.com
Print = No
Save = Yes
Range = yesterday
Detail = Med
Service = All
Format = html
Encode = base64
EOF

    # Create custom service for RT System logs
    sudo mkdir -p /etc/logwatch/conf/services
    sudo tee /etc/logwatch/conf/services/rt-system.conf > /dev/null <<EOF
Title = "RT System Application"
LogFile = rt-system
*OnlyService = rt-system
*RemoveHeaders = No
EOF

    sudo mkdir -p /etc/logwatch/conf/logfiles
    sudo tee /etc/logwatch/conf/logfiles/rt-system.conf > /dev/null <<EOF
LogFile = $PROJECT_PATH/storage/logs/*.log
Archive = $PROJECT_PATH/storage/logs/*.log.*
*ExpandRepeats = Yes
*ApplyStdDate = Yes
EOF

    log "Logwatch configured"
}

# Create security audit script
create_security_audit() {
    log "Creating security audit script..."

    sudo tee /usr/local/bin/rt-security-audit.sh > /dev/null <<'EOF'
#!/bin/bash

# RT System Security Audit Script

AUDIT_LOG="/var/log/rt-security-audit.log"
EMAIL="admin@your-domain.com"

echo "=== RT System Security Audit - $(date) ===" > "$AUDIT_LOG"

# Check for failed login attempts
echo "" >> "$AUDIT_LOG"
echo "=== Failed Login Attempts ===" >> "$AUDIT_LOG"
grep "authentication failure" /var/log/auth.log | tail -10 >> "$AUDIT_LOG" 2>/dev/null || echo "No recent failed logins" >> "$AUDIT_LOG"

# Check Fail2Ban status
echo "" >> "$AUDIT_LOG"
echo "=== Fail2Ban Status ===" >> "$AUDIT_LOG"
fail2ban-client status >> "$AUDIT_LOG" 2>/dev/null || echo "Fail2Ban not running" >> "$AUDIT_LOG"

# Check for suspicious network connections
echo "" >> "$AUDIT_LOG"
echo "=== Network Connections ===" >> "$AUDIT_LOG"
netstat -tuln | grep LISTEN >> "$AUDIT_LOG"

# Check for rootkits
echo "" >> "$AUDIT_LOG"
echo "=== Rootkit Scan ===" >> "$AUDIT_LOG"
rkhunter --check --sk --nocolors >> "$AUDIT_LOG" 2>/dev/null || echo "Rootkit scan failed" >> "$AUDIT_LOG"

# Check file permissions
echo "" >> "$AUDIT_LOG"
echo "=== File Permission Check ===" >> "$AUDIT_LOG"
find /var/www/rt-system -type f -perm /o+w -ls >> "$AUDIT_LOG" 2>/dev/null || echo "No world-writable files found" >> "$AUDIT_LOG"

# Check for SUID files
echo "" >> "$AUDIT_LOG"
echo "=== SUID Files ===" >> "$AUDIT_LOG"
find / -type f -perm -4000 -ls 2>/dev/null | head -20 >> "$AUDIT_LOG"

# Check disk usage
echo "" >> "$AUDIT_LOG"
echo "=== Disk Usage ===" >> "$AUDIT_LOG"
df -h >> "$AUDIT_LOG"

# Check running processes
echo "" >> "$AUDIT_LOG"
echo "=== Running Processes ===" >> "$AUDIT_LOG"
ps aux --sort=-%cpu | head -20 >> "$AUDIT_LOG"

# Send email if any issues found
if grep -q "INFECTED\|WARNING\|CRITICAL" "$AUDIT_LOG"; then
    mail -s "RT System Security Audit - Issues Found" "$EMAIL" < "$AUDIT_LOG"
else
    echo "Security audit completed - no issues found" | mail -s "RT System Security Audit - Clean" "$EMAIL"
fi
EOF

    sudo chmod +x /usr/local/bin/rt-security-audit.sh

    # Schedule weekly security audits
    (crontab -l 2>/dev/null; echo "0 2 * * 0 /usr/local/bin/rt-security-audit.sh") | sudo crontab -

    log "Security audit script created and scheduled"
}

# Main hardening function
main() {
    log "Starting security hardening for RT System..."

    # Check if running as root
    if [[ $EUID -ne 0 ]]; then
        error "This script must be run as root"
    fi

    install_security_tools
    configure_firewall
    configure_fail2ban
    secure_ssh
    configure_auto_updates
    harden_kernel
    secure_file_permissions
    configure_aide
    configure_logwatch
    create_security_audit

    log "Security hardening completed successfully!"
    warning "Important next steps:"
    info "1. Update email addresses in all configuration files"
    info "2. Configure SSH key-based authentication and disable password auth"
    info "3. Review and test all security configurations"
    info "4. Set up monitoring alerts for security events"
    info "5. Schedule regular security audits"
    info "6. Document all security procedures"
    warning "Remember to test all functionality after hardening!"
}

# Error handling
trap 'error "Security hardening failed at line $LINENO"' ERR

# Run main function
main "$@"
