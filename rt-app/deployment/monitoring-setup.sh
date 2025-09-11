#!/bin/bash

# RT Management System - Monitoring Setup Script
# This script sets up comprehensive monitoring for the production environment

set -e

# Configuration
PROJECT_NAME="rt-system"
PROJECT_PATH="/var/www/$PROJECT_NAME"
MONITORING_PATH="/var/monitoring/rt-system"
GRAFANA_PASSWORD="change_this_password"
ALERT_EMAIL="admin@your-domain.com"
SLACK_WEBHOOK=""

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

# Install monitoring tools
install_monitoring_tools() {
    log "Installing monitoring tools..."

    # Update system
    sudo apt update

    # Install Prometheus
    sudo useradd --no-create-home --shell /bin/false prometheus || true
    sudo mkdir -p /etc/prometheus /var/lib/prometheus
    sudo chown prometheus:prometheus /etc/prometheus /var/lib/prometheus

    cd /tmp
    wget https://github.com/prometheus/prometheus/releases/download/v2.40.0/prometheus-2.40.0.linux-amd64.tar.gz
    tar xvf prometheus-2.40.0.linux-amd64.tar.gz
    sudo cp prometheus-2.40.0.linux-amd64/prometheus /usr/local/bin/
    sudo cp prometheus-2.40.0.linux-amd64/promtool /usr/local/bin/
    sudo chown prometheus:prometheus /usr/local/bin/prometheus /usr/local/bin/promtool
    sudo cp -r prometheus-2.40.0.linux-amd64/consoles /etc/prometheus
    sudo cp -r prometheus-2.40.0.linux-amd64/console_libraries /etc/prometheus
    sudo chown -R prometheus:prometheus /etc/prometheus/consoles /etc/prometheus/console_libraries

    # Install Node Exporter
    sudo useradd --no-create-home --shell /bin/false node_exporter || true
    wget https://github.com/prometheus/node_exporter/releases/download/v1.4.0/node_exporter-1.4.0.linux-amd64.tar.gz
    tar xvf node_exporter-1.4.0.linux-amd64.tar.gz
    sudo cp node_exporter-1.4.0.linux-amd64/node_exporter /usr/local/bin/
    sudo chown node_exporter:node_exporter /usr/local/bin/node_exporter

    # Install Grafana
    wget -q -O - https://packages.grafana.com/gpg.key | sudo apt-key add -
    echo "deb https://packages.grafana.com/oss/deb stable main" | sudo tee -a /etc/apt/sources.list.d/grafana.list
    sudo apt update
    sudo apt install -y grafana

    # Install additional tools
    sudo apt install -y htop iotop nethogs iftop

    log "Monitoring tools installed successfully"
}

# Configure Prometheus
configure_prometheus() {
    log "Configuring Prometheus..."

    sudo tee /etc/prometheus/prometheus.yml > /dev/null <<EOF
global:
  scrape_interval: 15s
  evaluation_interval: 15s

rule_files:
  - "/etc/prometheus/rules/*.yml"

alerting:
  alertmanagers:
    - static_configs:
        - targets:
          - localhost:9093

scrape_configs:
  - job_name: 'prometheus'
    static_configs:
      - targets: ['localhost:9090']

  - job_name: 'node'
    static_configs:
      - targets: ['localhost:9100']

  - job_name: 'nginx'
    static_configs:
      - targets: ['localhost:9113']

  - job_name: 'php-fpm'
    static_configs:
      - targets: ['localhost:9253']

  - job_name: 'postgresql'
    static_configs:
      - targets: ['localhost:9187']

  - job_name: 'redis'
    static_configs:
      - targets: ['localhost:9121']

  - job_name: 'rt-system'
    static_configs:
      - targets: ['localhost:8000']
    metrics_path: '/metrics'
    scrape_interval: 30s
EOF

    sudo chown prometheus:prometheus /etc/prometheus/prometheus.yml

    # Create Prometheus service
    sudo tee /etc/systemd/system/prometheus.service > /dev/null <<EOF
[Unit]
Description=Prometheus
Wants=network-online.target
After=network-online.target

[Service]
User=prometheus
Group=prometheus
Type=simple
ExecStart=/usr/local/bin/prometheus \\
    --config.file /etc/prometheus/prometheus.yml \\
    --storage.tsdb.path /var/lib/prometheus/ \\
    --web.console.templates=/etc/prometheus/consoles \\
    --web.console.libraries=/etc/prometheus/console_libraries \\
    --web.listen-address=0.0.0.0:9090 \\
    --web.enable-lifecycle

[Install]
WantedBy=multi-user.target
EOF

    sudo systemctl daemon-reload
    sudo systemctl enable prometheus
    sudo systemctl start prometheus

    log "Prometheus configured and started"
}

# Configure Node Exporter
configure_node_exporter() {
    log "Configuring Node Exporter..."

    sudo tee /etc/systemd/system/node_exporter.service > /dev/null <<EOF
[Unit]
Description=Node Exporter
Wants=network-online.target
After=network-online.target

[Service]
User=node_exporter
Group=node_exporter
Type=simple
ExecStart=/usr/local/bin/node_exporter \\
    --collector.systemd \\
    --collector.processes \\
    --collector.interrupts \\
    --collector.tcpstat

[Install]
WantedBy=multi-user.target
EOF

    sudo systemctl daemon-reload
    sudo systemctl enable node_exporter
    sudo systemctl start node_exporter

    log "Node Exporter configured and started"
}

# Configure Grafana
configure_grafana() {
    log "Configuring Grafana..."

    # Configure Grafana
    sudo tee /etc/grafana/grafana.ini > /dev/null <<EOF
[default]
instance_name = RT System Monitoring

[server]
protocol = http
http_addr = 127.0.0.1
http_port = 3000
domain = localhost
root_url = http://localhost:3000/

[database]
type = sqlite3
path = /var/lib/grafana/grafana.db

[security]
admin_user = admin
admin_password = $GRAFANA_PASSWORD
secret_key = $(openssl rand -base64 32)

[users]
allow_sign_up = false
default_theme = dark

[auth.anonymous]
enabled = false

[smtp]
enabled = true
host = localhost:587
user =
password =
from_address = admin@your-domain.com
from_name = RT System Monitoring

[alerting]
enabled = true
execute_alerts = true

[log]
mode = file
level = info
EOF

    sudo systemctl enable grafana-server
    sudo systemctl start grafana-server

    log "Grafana configured and started"
}

# Setup alert rules
setup_alert_rules() {
    log "Setting up alert rules..."

    sudo mkdir -p /etc/prometheus/rules

    # Create alert rules
    sudo tee /etc/prometheus/rules/rt-system.yml > /dev/null <<EOF
groups:
  - name: rt-system-alerts
    rules:
      - alert: HighCPUUsage
        expr: 100 - (avg by(instance) (irate(node_cpu_seconds_total{mode="idle"}[5m])) * 100) > 80
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "High CPU usage detected"
          description: "CPU usage is above 80% for more than 5 minutes"

      - alert: HighMemoryUsage
        expr: (1 - (node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes)) * 100 > 85
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "High memory usage detected"
          description: "Memory usage is above 85% for more than 5 minutes"

      - alert: DiskSpaceLow
        expr: (1 - (node_filesystem_avail_bytes{fstype!="tmpfs"} / node_filesystem_size_bytes{fstype!="tmpfs"})) * 100 > 85
        for: 10m
        labels:
          severity: critical
        annotations:
          summary: "Disk space is running low"
          description: "Disk usage is above 85% for more than 10 minutes"

      - alert: ApplicationDown
        expr: up{job="rt-system"} == 0
        for: 1m
        labels:
          severity: critical
        annotations:
          summary: "RT System application is down"
          description: "The RT System application has been down for more than 1 minute"

      - alert: DatabaseDown
        expr: up{job="postgresql"} == 0
        for: 1m
        labels:
          severity: critical
        annotations:
          summary: "PostgreSQL database is down"
          description: "The PostgreSQL database has been down for more than 1 minute"

      - alert: HighResponseTime
        expr: avg(nginx_http_request_duration_seconds{quantile="0.95"}) > 2
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "High response time detected"
          description: "95th percentile response time is above 2 seconds"

      - alert: HighErrorRate
        expr: rate(nginx_http_requests_total{status=~"5.."}[5m]) / rate(nginx_http_requests_total[5m]) > 0.05
        for: 5m
        labels:
          severity: critical
        annotations:
          summary: "High error rate detected"
          description: "Error rate is above 5% for more than 5 minutes"
EOF

    sudo chown prometheus:prometheus /etc/prometheus/rules/rt-system.yml

    # Reload Prometheus
    sudo systemctl reload prometheus

    log "Alert rules configured"
}

# Setup log monitoring
setup_log_monitoring() {
    log "Setting up log monitoring..."

    # Create log monitoring script
    sudo tee /usr/local/bin/rt-log-monitor.sh > /dev/null <<'EOF'
#!/bin/bash

LOG_FILE="/var/log/rt-system-monitor.log"
ALERT_EMAIL="admin@your-domain.com"
CHECK_INTERVAL=60

# Function to send alert
send_alert() {
    local message="$1"
    echo "$(date): $message" >> "$LOG_FILE"
    echo "$message" | mail -s "RT System Alert" "$ALERT_EMAIL" 2>/dev/null || true
}

# Monitor application logs for errors
monitor_app_logs() {
    local error_count=$(tail -n 100 /var/www/rt-system/storage/logs/laravel.log | grep -c "ERROR" || true)
    if [ "$error_count" -gt 5 ]; then
        send_alert "High number of application errors detected: $error_count errors in last 100 log entries"
    fi
}

# Monitor Nginx logs for high error rate
monitor_nginx_logs() {
    local error_count=$(tail -n 1000 /var/log/nginx/rt-system-error.log | grep -c "$(date '+%Y/%m/%d %H:')" || true)
    if [ "$error_count" -gt 10 ]; then
        send_alert "High number of Nginx errors detected: $error_count errors in current hour"
    fi
}

# Monitor PHP-FPM logs
monitor_php_logs() {
    local error_count=$(tail -n 100 /var/log/php8.3-fpm.log | grep -c "ERROR" || true)
    if [ "$error_count" -gt 5 ]; then
        send_alert "High number of PHP-FPM errors detected: $error_count errors in last 100 log entries"
    fi
}

# Main monitoring loop
while true; do
    monitor_app_logs
    monitor_nginx_logs
    monitor_php_logs
    sleep $CHECK_INTERVAL
done
EOF

    sudo chmod +x /usr/local/bin/rt-log-monitor.sh

    # Create systemd service for log monitoring
    sudo tee /etc/systemd/system/rt-log-monitor.service > /dev/null <<EOF
[Unit]
Description=RT System Log Monitor
After=network.target

[Service]
Type=simple
User=www-data
ExecStart=/usr/local/bin/rt-log-monitor.sh
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

    sudo systemctl daemon-reload
    sudo systemctl enable rt-log-monitor
    sudo systemctl start rt-log-monitor

    log "Log monitoring configured and started"
}

# Setup performance monitoring
setup_performance_monitoring() {
    log "Setting up performance monitoring..."

    # Create performance monitoring script
    sudo tee /usr/local/bin/rt-performance-monitor.sh > /dev/null <<'EOF'
#!/bin/bash

METRICS_FILE="/var/log/rt-performance-metrics.log"
ALERT_EMAIL="admin@your-domain.com"

# Collect system metrics
collect_metrics() {
    local timestamp=$(date "+%Y-%m-%d %H:%M:%S")
    local cpu_usage=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | sed 's/%us,//')
    local memory_usage=$(free | grep Mem | awk '{printf "%.1f", $3/$2 * 100.0}')
    local disk_usage=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
    local load_avg=$(uptime | awk -F'load average:' '{print $2}' | cut -d, -f1 | xargs)

    # Log metrics
    echo "$timestamp,CPU:$cpu_usage,Memory:$memory_usage%,Disk:$disk_usage%,LoadAvg:$load_avg" >> "$METRICS_FILE"

    # Check thresholds and alert
    if (( $(echo "$cpu_usage > 80" | bc -l) )); then
        echo "High CPU usage: $cpu_usage%" | mail -s "RT System CPU Alert" "$ALERT_EMAIL" 2>/dev/null || true
    fi

    if (( $(echo "$memory_usage > 85" | bc -l) )); then
        echo "High memory usage: $memory_usage%" | mail -s "RT System Memory Alert" "$ALERT_EMAIL" 2>/dev/null || true
    fi

    if [ "$disk_usage" -gt 85 ]; then
        echo "High disk usage: $disk_usage%" | mail -s "RT System Disk Alert" "$ALERT_EMAIL" 2>/dev/null || true
    fi
}

# Collect application metrics
collect_app_metrics() {
    local response_time=$(curl -o /dev/null -s -w '%{time_total}' http://localhost:8000 || echo "0")
    local http_status=$(curl -o /dev/null -s -w '%{http_code}' http://localhost:8000 || echo "000")

    if [ "$http_status" != "200" ]; then
        echo "Application not responding. HTTP Status: $http_status" | mail -s "RT System App Alert" "$ALERT_EMAIL" 2>/dev/null || true
    fi

    if (( $(echo "$response_time > 3" | bc -l) )); then
        echo "Slow response time: ${response_time}s" | mail -s "RT System Performance Alert" "$ALERT_EMAIL" 2>/dev/null || true
    fi
}

# Main execution
collect_metrics
collect_app_metrics

# Rotate log file if it gets too large
if [ -f "$METRICS_FILE" ] && [ $(stat -c%s "$METRICS_FILE") -gt 10485760 ]; then
    mv "$METRICS_FILE" "$METRICS_FILE.old"
    touch "$METRICS_FILE"
fi
EOF

    sudo chmod +x /usr/local/bin/rt-performance-monitor.sh

    # Add to crontab
    (crontab -l 2>/dev/null; echo "*/5 * * * * /usr/local/bin/rt-performance-monitor.sh") | sudo crontab -

    log "Performance monitoring configured"
}

# Create monitoring dashboard
create_monitoring_dashboard() {
    log "Creating monitoring dashboard..."

    mkdir -p "$MONITORING_PATH"

    # Create simple HTML dashboard
    tee "$MONITORING_PATH/dashboard.html" > /dev/null <<'EOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RT System Monitoring Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .metric { display: inline-block; margin: 10px 20px 10px 0; }
        .metric-value { font-size: 24px; font-weight: bold; color: #2196F3; }
        .metric-label { font-size: 14px; color: #666; }
        .status-ok { color: #4CAF50; }
        .status-warning { color: #FF9800; }
        .status-error { color: #F44336; }
        .refresh-btn { background: #2196F3; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>RT System Monitoring Dashboard</h1>

        <div class="card">
            <h3>System Status</h3>
            <button class="refresh-btn" onclick="location.reload()">Refresh</button>
            <div id="system-metrics">
                <div class="metric">
                    <div class="metric-value status-ok">●</div>
                    <div class="metric-label">Application</div>
                </div>
                <div class="metric">
                    <div class="metric-value status-ok">●</div>
                    <div class="metric-label">Database</div>
                </div>
                <div class="metric">
                    <div class="metric-value status-ok">●</div>
                    <div class="metric-label">Web Server</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Performance Metrics</h3>
            <div id="performance-metrics">
                <div class="metric">
                    <div class="metric-value">--</div>
                    <div class="metric-label">CPU Usage</div>
                </div>
                <div class="metric">
                    <div class="metric-value">--</div>
                    <div class="metric-label">Memory Usage</div>
                </div>
                <div class="metric">
                    <div class="metric-value">--</div>
                    <div class="metric-label">Disk Usage</div>
                </div>
                <div class="metric">
                    <div class="metric-value">--</div>
                    <div class="metric-label">Load Average</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Quick Links</h3>
            <p><a href="http://localhost:9090" target="_blank">Prometheus</a> | <a href="http://localhost:3000" target="_blank">Grafana</a></p>
        </div>

        <div class="card">
            <h3>Recent Alerts</h3>
            <div id="recent-alerts">
                <p>No recent alerts</p>
            </div>
        </div>
    </div>

    <script>
        // Auto refresh every 30 seconds
        setTimeout(() => location.reload(), 30000);
    </script>
</body>
</html>
EOF

    # Create simple web server for dashboard
    sudo tee /etc/systemd/system/rt-monitoring-dashboard.service > /dev/null <<EOF
[Unit]
Description=RT System Monitoring Dashboard
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=$MONITORING_PATH
ExecStart=/usr/bin/python3 -m http.server 8080
Restart=always

[Install]
WantedBy=multi-user.target
EOF

    sudo systemctl daemon-reload
    sudo systemctl enable rt-monitoring-dashboard
    sudo systemctl start rt-monitoring-dashboard

    log "Monitoring dashboard created and started on port 8080"
}

# Main setup function
main() {
    log "Setting up comprehensive monitoring for RT System..."

    install_monitoring_tools
    configure_prometheus
    configure_node_exporter
    configure_grafana
    setup_alert_rules
    setup_log_monitoring
    setup_performance_monitoring
    create_monitoring_dashboard

    log "Monitoring setup completed successfully!"
    info "Access points:"
    info "- Prometheus: http://localhost:9090"
    info "- Grafana: http://localhost:3000 (admin/$GRAFANA_PASSWORD)"
    info "- Monitoring Dashboard: http://localhost:8080"
    warning "Remember to configure firewall rules for monitoring ports"
    warning "Update email configurations in monitoring scripts"
}

# Error handling
trap 'error "Monitoring setup failed at line $LINENO"' ERR

# Run main function
main "$@"
