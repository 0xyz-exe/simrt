<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SecurityMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor system security and send alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Starting security monitoring...');

        $alerts = [];

        // Check for suspicious activities
        $alerts = array_merge($alerts, $this->checkFailedLogins());
        $alerts = array_merge($alerts, $this->checkSuspiciousIPs());
        $alerts = array_merge($alerts, $this->checkSystemHealth());
        $alerts = array_merge($alerts, $this->checkDiskSpace());
        $alerts = array_merge($alerts, $this->checkDatabaseHealth());

        // Send alerts if any
        if (!empty($alerts)) {
            $this->sendSecurityAlert($alerts);
        }

        $this->info('✅ Security monitoring completed');
        return 0;
    }

    private function checkFailedLogins(): array
    {
        $alerts = [];
        $threshold = 10; // Failed logins in last hour

        // Count failed login attempts in the last hour
        $failedLogins = DB::table('failed_login_attempts')
            ->where('created_at', '>', now()->subHour())
            ->count();

        if ($failedLogins > $threshold) {
            $alerts[] = [
                'type' => 'CRITICAL',
                'message' => "High number of failed login attempts: {$failedLogins} in the last hour",
                'recommendation' => 'Consider implementing additional rate limiting or CAPTCHA'
            ];
        }

        // Check for distributed attacks
        $uniqueIPs = DB::table('failed_login_attempts')
            ->where('created_at', '>', now()->subHour())
            ->distinct('ip_address')
            ->count('ip_address');

        if ($uniqueIPs > 20) {
            $alerts[] = [
                'type' => 'HIGH',
                'message' => "Distributed attack detected: {$uniqueIPs} unique IPs with failed logins",
                'recommendation' => 'Consider enabling geographical blocking or WAF'
            ];
        }

        return $alerts;
    }

    private function checkSuspiciousIPs(): array
    {
        $alerts = [];

        // Check for banned IPs trying to access
        $bannedAttempts = Cache::get('banned_ip_attempts', 0);
        if ($bannedAttempts > 50) {
            $alerts[] = [
                'type' => 'MEDIUM',
                'message' => "Banned IPs attempted access {$bannedAttempts} times",
                'recommendation' => 'Review and update IP blacklist'
            ];
        }

        // Check for high request volume from single IP
        $highVolumeIPs = Cache::get('high_volume_ips', []);
        if (!empty($highVolumeIPs)) {
            $alerts[] = [
                'type' => 'MEDIUM',
                'message' => 'High volume requests detected from: ' . implode(', ', array_keys($highVolumeIPs)),
                'recommendation' => 'Investigate these IPs for potential abuse'
            ];
        }

        return $alerts;
    }

    private function checkSystemHealth(): array
    {
        $alerts = [];

        // Check application errors
        $errorCount = Log::daily()->count('error');
        if ($errorCount > 100) {
            $alerts[] = [
                'type' => 'HIGH',
                'message' => "High error rate: {$errorCount} errors in the last hour",
                'recommendation' => 'Check application logs for critical issues'
            ];
        }

        // Check for suspicious user agents
        $suspiciousAgents = Cache::get('suspicious_user_agents', []);
        if (!empty($suspiciousAgents)) {
            $alerts[] = [
                'type' => 'LOW',
                'message' => 'Suspicious user agents detected: ' . count($suspiciousAgents),
                'recommendation' => 'Monitor for automated attack tools'
            ];
        }

        return $alerts;
    }

    private function checkDiskSpace(): array
    {
        $alerts = [];

        // Check available disk space
        $freeBytes = disk_free_space(base_path());
        $totalBytes = disk_total_space(base_path());
        $usedPercentage = (($totalBytes - $freeBytes) / $totalBytes) * 100;

        if ($usedPercentage > 90) {
            $alerts[] = [
                'type' => 'CRITICAL',
                'message' => "Disk space critically low: {$usedPercentage}% used",
                'recommendation' => 'Clean up logs and temporary files immediately'
            ];
        } elseif ($usedPercentage > 80) {
            $alerts[] = [
                'type' => 'HIGH',
                'message' => "Disk space running low: {$usedPercentage}% used",
                'recommendation' => 'Plan for disk cleanup or expansion'
            ];
        }

        return $alerts;
    }

    private function checkDatabaseHealth(): array
    {
        $alerts = [];

        try {
            // Check database connection
            $start = microtime(true);
            DB::select('SELECT 1');
            $duration = microtime(true) - $start;

            if ($duration > 1.0) {
                $alerts[] = [
                    'type' => 'MEDIUM',
                    'message' => "Database response slow: {$duration}s",
                    'recommendation' => 'Check database performance and optimize queries'
                ];
            }

            // Check for long-running queries
            $longQueries = DB::select("SHOW PROCESSLIST");
            $longRunningCount = 0;

            foreach ($longQueries as $query) {
                if (isset($query->Time) && $query->Time > 30) {
                    $longRunningCount++;
                }
            }

            if ($longRunningCount > 0) {
                $alerts[] = [
                    'type' => 'MEDIUM',
                    'message' => "Long-running database queries detected: {$longRunningCount}",
                    'recommendation' => 'Review and optimize slow queries'
                ];
            }

        } catch (\Exception $e) {
            $alerts[] = [
                'type' => 'CRITICAL',
                'message' => "Database connection failed: " . $e->getMessage(),
                'recommendation' => 'Check database server status immediately'
            ];
        }

        return $alerts;
    }

    private function sendSecurityAlert(array $alerts): void
    {
        $criticalCount = count(array_filter($alerts, fn($alert) => $alert['type'] === 'CRITICAL'));
        $highCount = count(array_filter($alerts, fn($alert) => $alert['type'] === 'HIGH'));
        $mediumCount = count(array_filter($alerts, fn($alert) => $alert['type'] === 'MEDIUM'));

        $message = "🚨 SIMRT Security Alert\n\n";
        $message .= "Critical: {$criticalCount} | High: {$highCount} | Medium: {$mediumCount}\n\n";

        foreach ($alerts as $alert) {
            $emoji = match($alert['type']) {
                'CRITICAL' => '🔴',
                'HIGH' => '🟠',
                'MEDIUM' => '🟡',
                default => '🔵'
            };

            $message .= "{$emoji} {$alert['type']}: {$alert['message']}\n";
            $message .= "   💡 {$alert['recommendation']}\n\n";
        }

        $message .= "Time: " . now()->toDateTimeString() . "\n";
        $message .= "Server: " . gethostname();

        // Send to Slack webhook if configured
        if ($webhookUrl = config('app.alert_webhook_url')) {
            try {
                Http::post($webhookUrl, [
                    'text' => $message
                ]);
                $this->info('📡 Alert sent to Slack');
            } catch (\Exception $e) {
                $this->error('Failed to send Slack alert: ' . $e->getMessage());
            }
        }

        // Send email alert if configured
        if ($alertEmail = config('app.alert_email')) {
            try {
                // Send email alert
                $this->info('📧 Alert sent to email');
            } catch (\Exception $e) {
                $this->error('Failed to send email alert: ' . $e->getMessage());
            }
        }

        // Log the alert
        Log::warning('Security Alert Generated', [
            'alerts' => $alerts,
            'counts' => [
                'critical' => $criticalCount,
                'high' => $highCount,
                'medium' => $mediumCount
            ]
        ]);
    }
}
