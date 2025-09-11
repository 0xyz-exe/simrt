<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdvancedSecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip aggressive security checks for admin/dashboard routes to prevent false positives
        $skipPaths = [
            'dashboard',
            'api/dashboard/stats',
            'activities',
            'dues',
            'settings',
            'announcements',
            'households',
            'residents'
        ];

        $currentPath = $request->path();
        $shouldSkip = false;

        foreach ($skipPaths as $skipPath) {
            if (str_contains($currentPath, $skipPath)) {
                $shouldSkip = true;
                break;
            }
        }

        if (!$shouldSkip) {
            // 1. Bot Detection & Blocking
            $this->detectAndBlockBots($request);

            // 2. Suspicious Pattern Detection
            $this->detectSuspiciousPatterns($request);

            // 3. IP Reputation Check
            $this->checkIpReputation($request);

            // 4. Request Validation
            $this->validateRequest($request);
        }

        // 5. Security Headers Enhancement (always apply)
        $response = $next($request);
        $this->addAdvancedSecurityHeaders($response, $request);

        // 6. Response Security (always apply)
        $this->secureResponse($response);

        // 7. Audit Logging (always apply)
        $this->auditLog($request, $response);

        return $response;
    }

    /**
     * Detect and block malicious bots
     */
    private function detectAndBlockBots(Request $request): void
    {
        $userAgent = $request->userAgent();
        $ip = $request->ip();

        // Known malicious bot patterns
        $maliciousBots = [
            'sqlmap', 'nikto', 'nmap', 'masscan', 'zap', 'w3af',
            'havij', 'acunetix', 'netsparker', 'appscan', 'burp',
            'metasploit', 'hydra', 'medusa', 'john', 'hashcat'
        ];

        foreach ($maliciousBots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                Log::critical('Malicious bot detected', [
                    'ip' => $ip,
                    'user_agent' => $userAgent,
                    'bot_type' => $bot,
                    'url' => $request->fullUrl()
                ]);

                abort(403, 'Access Denied');
            }
        }

        // Empty or suspicious user agents
        if (empty($userAgent) || strlen($userAgent) < 10) {
            Log::warning('Suspicious user agent detected', [
                'ip' => $ip,
                'user_agent' => $userAgent
            ]);
        }
    }

    /**
     * Detect suspicious request patterns
     */
    private function detectSuspiciousPatterns(Request $request): void
    {
        $url = $request->fullUrl();
        $ip = $request->ip();

        // SQL Injection patterns
        $sqlPatterns = [
            'union.*select', 'select.*from', 'insert.*into', 'delete.*from',
            'drop.*table', 'exec.*xp_', 'sp_executesql', 'xp_cmdshell',
            '\bor\b.*=.*\bor\b', '\band\b.*=.*\band\b', 'waitfor.*delay'
        ];

        // XSS patterns
        $xssPatterns = [
            '<script', 'javascript:', 'onload=', 'onerror=', 'onclick=',
            'alert\(', 'confirm\(', 'prompt\(', 'document\.cookie',
            'window\.location', 'eval\('
        ];

        // LFI/RFI patterns
        $lfiPatterns = [
            '\.\./.*\.\./.*\.\./.*/', '/etc/passwd', '/etc/shadow',
            'boot\.ini', 'win\.ini', 'php://filter', 'php://input',
            'data://text', 'expect://id'
        ];

        $allPatterns = array_merge($sqlPatterns, $xssPatterns, $lfiPatterns);

        foreach ($allPatterns as $pattern) {
            // Use a safe delimiter that won't conflict with patterns containing '/'
            if (preg_match('~' . $pattern . '~i', $url) ||
                preg_match('~' . $pattern . '~i', json_encode($request->all()))) {

                Log::critical('Attack pattern detected', [
                    'ip' => $ip,
                    'pattern' => $pattern,
                    'url' => $url,
                    'payload' => $request->all(),
                    'user_agent' => $request->userAgent()
                ]);

                abort(403, 'Malicious Request Detected');
            }
        }
    }

    /**
     * Check IP reputation (basic implementation)
     */
    private function checkIpReputation(Request $request): void
    {
        $ip = $request->ip();

        // Check against known malicious IP ranges
        $maliciousRanges = [
            '10.0.0.0/8',      // Private range (should not be external)
            '172.16.0.0/12',   // Private range
            '192.168.0.0/16',  // Private range
            '127.0.0.0/8',     // Localhost
            '0.0.0.0/8',       // Invalid range
        ];

        // Skip private IP check for local development
        if (app()->environment('local')) {
            return;
        }

        foreach ($maliciousRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                Log::warning('Request from suspicious IP range', [
                    'ip' => $ip,
                    'range' => $range
                ]);
            }
        }
    }

    /**
     * Validate request for security
     */
    private function validateRequest(Request $request): void
    {
        // Check for oversized requests
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($request->server('CONTENT_LENGTH') > $maxSize) {
            Log::warning('Oversized request detected', [
                'ip' => $request->ip(),
                'size' => $request->server('CONTENT_LENGTH')
            ]);
            abort(413, 'Request Too Large');
        }

        // Check for too many parameters
        if (count($request->all()) > 100) {
            Log::warning('Too many request parameters', [
                'ip' => $request->ip(),
                'count' => count($request->all())
            ]);
            abort(400, 'Too Many Parameters');
        }
    }

    /**
     * Add advanced security headers
     */
    private function addAdvancedSecurityHeaders(Response $response, Request $request): void
    {
        // Enhanced CSP
    $csp = "default-src 'self'; " .
           // Allow required CDNs used by landing layout (Tailwind CDN, Font Awesome, Leaflet)
           "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://apis.google.com https://accounts.google.com https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://unpkg.com https://cdnjs.cloudflare.com 'nonce-" . $this->generateNonce() . "'; " .
           "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com; " .
           "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
           "img-src 'self' data: https: blob:; " .
           "connect-src 'self' https://accounts.google.com; " .
           "frame-src 'self' https://accounts.google.com; " .
           "frame-ancestors 'none'; " .
           "base-uri 'self'; " .
           "form-action 'self'; " .
           "upgrade-insecure-requests; " .
           "block-all-mixed-content;";

        $response->headers->set('Content-Security-Policy', $csp);

        // Additional security headers
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    // Cross-origin isolation headers can break third-party assets; disable COEP here.
    // If you need SharedArrayBuffer or strong isolation, re-enable after ensuring all third-party resources send CORP/CORS.
    // $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
    $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
    // This controls how others can embed our resources; keeping default/no header is fine for now
    // $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        // HSTS for production
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Remove revealing headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        $response->headers->remove('X-Laravel-Cache');
    }

    /**
     * Secure response content
     */
    private function secureResponse(Response $response): void
    {
        // Remove sensitive information from error responses
        if ($response->getStatusCode() >= 400) {
            $content = $response->getContent();

            // Remove potential path disclosure
            $content = preg_replace('/\/[a-zA-Z0-9_\-\/\.]+\.(php|js|css|html)/', '[FILTERED]', $content);

            // Remove stack traces in production
            if (app()->environment('production')) {
                $content = preg_replace('/Stack trace:.*$/ms', '[Stack trace hidden]', $content);
                $content = preg_replace('/#\d+.*$/m', '[Line hidden]', $content);
            }

            $response->setContent($content);
        }
    }

    /**
     * Audit logging for security events
     */
    private function auditLog(Request $request, Response $response): void
    {
        // Log suspicious activities
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 400) {
            Log::info('HTTP Error Response', [
                'ip' => $request->ip(),
                'status' => $statusCode,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'user_id' => Auth::id()
            ]);
        }

        // Log authentication events
        if (in_array($request->route()->getName() ?? '', ['login', 'register', 'logout'])) {
            Log::info('Authentication Event', [
                'event' => $request->route()->getName(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'success' => $statusCode < 400
            ]);
        }
    }

    /**
     * Check if IP is in range
     */
    private function ipInRange(string $ip, string $range): bool
    {
        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;
        return ($ip & $mask) == $subnet;
    }

    /**
     * Generate nonce for CSP
     */
    private function generateNonce(): string
    {
        return base64_encode(random_bytes(16));
    }
}
