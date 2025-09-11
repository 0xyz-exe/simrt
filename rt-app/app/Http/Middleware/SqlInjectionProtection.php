<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SqlInjectionProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->scanForSqlInjection($request);
        return $next($request);
    }

    /**
     * Scan request for SQL injection patterns
     */
    private function scanForSqlInjection(Request $request): void
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // Advanced SQL injection patterns
        $sqlPatterns = [
            // Union-based injection
            '/(\s|^)(union)(\s)+.*(select)/i',
            '/(\s|^)(select)(\s)+.*(\s)+(from)/i',

            // Boolean-based injection
            '/(\s|^)(and|or)(\s)+\d+(\s)*=(\s)*\d+/i',
            '/(\s|^)(and|or)(\s)+\w+(\s)*=(\s)*\w+/i',

            // Error-based injection
            '/extractvalue\(|updatexml\(|exp\(/i',
            '/cast\(.*as\s+int\)|convert\(.*,\s*int\)/i',

            // Time-based injection
            '/waitfor(\s)+delay|sleep\(|benchmark\(/i',
            '/pg_sleep\(|dbms_pipe\.receive_message/i',

            // Stacked queries
            '/;(\s)*(insert|update|delete|drop|create|alter)/i',

            // Database specific functions
            '/@@version|@@servername|@@servicename/i',
            '/user\(\)|database\(\)|version\(\)/i',
            '/information_schema\.|sys\.|pg_/i',

            // Comment sequences
            '/(\/\*.*\*\/|--[\s\S]*|#.*)/i',

            // Special characters sequences
            '/[\x00-\x08\x0b\x0c\x0e-\x1f\x7f]/i',

            // SQL keywords in specific contexts
            '/exec(\s)+xp_|sp_executesql|xp_cmdshell/i',
            '/load_file\(|into(\s)+outfile|into(\s)+dumpfile/i',

            // Advanced evasion techniques
            '/0x[0-9a-f]+/i',                          // Hex encoding
            '/char\(\d+\)|ascii\(\w+\)/i',            // Character encoding
            '/substring\(.*,\d+,\d+\)/i',             // Substring functions
            '/concat\(.*\)|group_concat\(/i',         // Concatenation

            // NoSQL injection patterns
            '/\$where|\$ne|\$gt|\$lt|\$regex/i',
            '/{\s*\$\w+\s*:/i',
        ];

        // Check all input sources
        $inputs = array_merge(
            $request->all(),
            [$request->getRequestUri()],
            $request->headers->all(),
            [$request->getContent()]
        );

        foreach ($inputs as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            if (!is_string($value)) {
                continue;
            }

            foreach ($sqlPatterns as $pattern) {
                if (preg_match($pattern, $value, $matches)) {
                    Log::critical('SQL Injection attempt detected', [
                        'ip' => $ip,
                        'user_agent' => $userAgent,
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'field' => $key,
                        'pattern' => $pattern,
                        'matched' => $matches[0] ?? '',
                        'payload' => substr($value, 0, 500), // Limit payload size in log
                        'timestamp' => now()->toISOString(),
                        'session_id' => session()->getId(),
                        'referrer' => $request->header('referer')
                    ]);

                    abort(403, 'Malicious request detected');
                }
            }

            // Additional entropy-based detection
            $this->checkSuspiciousEntropy($value, $key, $request);
        }
    }

    /**
     * Check for suspicious entropy that might indicate injection attempts
     */
    private function checkSuspiciousEntropy(string $value, $key, Request $request): void
    {
        // Skip checking for certain fields that naturally have high entropy
        $skipFields = ['password', 'token', 'csrf_token', '_token', 'api_key'];
        if (in_array($key, $skipFields)) {
            return;
        }

        // Calculate entropy
        $entropy = $this->calculateEntropy($value);

        // High entropy combined with SQL keywords is suspicious
        if ($entropy > 4.5 && strlen($value) > 20) {
            $sqlKeywords = ['select', 'union', 'insert', 'update', 'delete', 'drop', 'alter'];
            $suspiciousWords = 0;

            foreach ($sqlKeywords as $keyword) {
                if (stripos($value, $keyword) !== false) {
                    $suspiciousWords++;
                }
            }

            if ($suspiciousWords >= 2) {
                Log::warning('High entropy SQL injection attempt', [
                    'ip' => $request->ip(),
                    'field' => $key,
                    'entropy' => $entropy,
                    'sql_keywords' => $suspiciousWords,
                    'value_length' => strlen($value),
                    'payload_sample' => substr($value, 0, 100)
                ]);

                abort(403, 'Suspicious request pattern detected');
            }
        }
    }

    /**
     * Calculate Shannon entropy of a string
     */
    private function calculateEntropy(string $string): float
    {
        $length = strlen($string);
        if ($length <= 1) {
            return 0;
        }

        $frequencies = array_count_values(str_split($string));
        $entropy = 0;

        foreach ($frequencies as $frequency) {
            $probability = $frequency / $length;
            $entropy -= $probability * log($probability, 2);
        }

        return $entropy;
    }
}
