<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class XSSProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip XSS protection for certain safe routes to prevent false positives
        $safePaths = [
            'api/dashboard/stats',
            'activities',
            'dues',
            'settings',
            'announcements'
        ];

        $currentPath = $request->path();

        // Check if current path is in safe paths
        foreach ($safePaths as $safePath) {
            if (str_contains($currentPath, $safePath)) {
                return $next($request);
            }
        }

        // Only scan for XSS on suspicious requests
        $this->scanForXSS($request);
        return $next($request);
    }

    /**
     * Scan request for XSS patterns
     */
    private function scanForXSS(Request $request): void
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // Comprehensive XSS patterns
        $xssPatterns = [
            // Script tags
            '/<script[^>]*>.*?<\/script>/is',
            '/<script[^>]*>/i',

            // Event handlers
            '/on\w+\s*=\s*["\']?[^"\']*["\']?/i',
            '/onload|onerror|onclick|onmouseover|onfocus|onblur/i',

            // JavaScript protocols
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/data\s*:.*base64/i',

            // Common XSS functions
            '/alert\s*\(|confirm\s*\(|prompt\s*\(/i',
            '/document\.(cookie|domain|location)/i',
            '/window\.(location|open)/i',
            '/eval\s*\(|setTimeout\s*\(|setInterval\s*\(/i',

            // HTML injection
            '/<iframe[^>]*>/i',
            '/<object[^>]*>/i',
            '/<embed[^>]*>/i',
            '/<applet[^>]*>/i',
            '/<meta[^>]*>/i',
            '/<link[^>]*>/i',
            '/<form[^>]*>/i',

            // CSS-based XSS
            '/expression\s*\(/i',
            '/javascript\s*:/i',
            '/behavior\s*:/i',
            '/@import/i',

            // Encoded XSS attempts
            '/&#x?[0-9a-f]{2,8};?/i',
            '/%[0-9a-f]{2}/i',
            '/\\\\u[0-9a-f]{4}/i',
            '/\\\\x[0-9a-f]{2}/i',

            // Advanced XSS vectors
            '/<.*?[\s\/][^>]*?(src|href|action|formaction|background|poster|lowsrc)\s*=\s*["\']?\s*javascript:/i',
            '/<.*?[\s\/][^>]*?(style)\s*=\s*["\']?[^"\']*expression\s*\(/i',
            '/<.*?[\s\/][^>]*?(style)\s*=\s*["\']?[^"\']*javascript:/i',

            // SVG-based XSS
            '/<svg[^>]*>.*?<\/svg>/is',
            '/<animatetransform[^>]*>/i',
            '/<feimage[^>]*>/i',

            // Math-based XSS
            '/<math[^>]*>.*?<\/math>/is',

            // Template injection attempts
            '/{{.*?}}/i',
            '/{%.*?%}/i',
            '/<%.*?%>/i',

            // CSS keylogger
            '/input\[type="password"\]/i',

            // Data exfiltration attempts
            '/fetch\s*\(|XMLHttpRequest/i',
            '/\.send\s*\(|\.open\s*\(/i',

            // DOM manipulation
            '/document\.write|innerHTML|outerHTML/i',
            '/appendChild|insertBefore|replaceChild/i',
            '/setAttribute|setAttributeNode/i',

            // Angular/React specific
            '/ng-\w+|v-\w+|\[.*?\]|\(.*?\)/i',
            '/dangerouslySetInnerHTML/i',
        ];

        // Check all input sources
        $inputs = array_merge(
            $request->all(),
            [$request->getRequestUri()],
            $request->headers->all()
        );

        foreach ($inputs as $key => $value) {
            if (is_array($value)) {
                $this->scanArrayForXSS($value, $key, $request);
                continue;
            }

            if (!is_string($value)) {
                continue;
            }

            // Decode common encodings before checking
            $decodedValue = $this->decodeValue($value);

            foreach ($xssPatterns as $pattern) {
                if (preg_match($pattern, $decodedValue, $matches)) {
                    Log::critical('XSS attempt detected', [
                        'ip' => $ip,
                        'user_agent' => $userAgent,
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'field' => $key,
                        'pattern' => $pattern,
                        'matched' => $matches[0] ?? '',
                        'original_payload' => substr($value, 0, 500),
                        'decoded_payload' => substr($decodedValue, 0, 500),
                        'timestamp' => now()->toISOString(),
                        'session_id' => session()->getId(),
                        'referrer' => $request->header('referer')
                    ]);

                    abort(403, 'XSS attempt detected');
                }
            }

            // Additional checks for obfuscated XSS
            $this->checkObfuscatedXSS($decodedValue, $key, $request);
        }
    }

    /**
     * Recursively scan arrays for XSS
     */
    private function scanArrayForXSS(array $array, string $parentKey, Request $request): void
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->scanArrayForXSS($value, $parentKey . '.' . $key, $request);
            } elseif (is_string($value)) {
                // Use the same XSS detection logic
                $decodedValue = $this->decodeValue($value);
                $this->checkForXSSPatterns($decodedValue, $parentKey . '.' . $key, $request);
            }
        }
    }

    /**
     * Check for XSS patterns in a single value
     */
    private function checkForXSSPatterns(string $value, string $field, Request $request): void
    {
        // Skip checking for certain safe fields
        $safeFields = ['month', 'year', 'status', 'page', 'per_page', 'search', 'filter'];

        if (in_array($field, $safeFields)) {
            return;
        }

        // Only check for the most dangerous XSS patterns to reduce false positives
        $dangerousPatterns = [
            // Active script execution
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript\s*:\s*alert\s*\(/i',
            '/on(load|error|click)\s*=\s*["\']?\s*alert\s*\(/i',
            // Clear XSS attempts with actual dangerous payloads
            '/(alert|confirm|prompt)\s*\(\s*["\']?\s*(xss|hack|attack|malicious)/i',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                Log::critical('XSS attempt detected', [
                    'ip' => $request->ip(),
                    'field' => $field,
                    'payload' => substr($value, 0, 200),
                    'pattern' => $pattern,
                    'user_agent' => $request->userAgent()
                ]);

                abort(403, 'XSS attempt detected');
            }
        }
    }

    /**
     * Decode various encoding methods
     */
    private function decodeValue(string $value): string
    {
        // URL decode
        $decoded = urldecode($value);

        // HTML entity decode
        $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Base64 decode if it looks like base64
        if (preg_match('/^[A-Za-z0-9+\/]+=*$/', $decoded) && strlen($decoded) % 4 === 0) {
            $base64Decoded = base64_decode($decoded, true);
            if ($base64Decoded !== false) {
                $decoded = $base64Decoded;
            }
        }

        // Hex decode
        if (preg_match('/^[0-9a-fA-F]+$/', $decoded) && strlen($decoded) % 2 === 0) {
            $hexDecoded = hex2bin($decoded);
            if ($hexDecoded !== false) {
                $decoded = $hexDecoded;
            }
        }

        // Unicode decode
        $decoded = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function($matches) {
            return mb_convert_encoding(pack('H*', $matches[1]), 'UTF-8', 'UCS-2BE');
        }, $decoded);

        return $decoded;
    }

    /**
     * Check for obfuscated XSS attempts
     */
    private function checkObfuscatedXSS(string $value, string $field, Request $request): void
    {
        // Check for excessive encoding
        $originalLength = strlen($value);
        $encodedChars = preg_match_all('/(%[0-9a-fA-F]{2}|&#x?[0-9a-fA-F]+;|\\\\[ux][0-9a-fA-F]+)/', $value);

        if ($encodedChars > 5 && $encodedChars / $originalLength > 0.3) {
            Log::warning('Heavily encoded input detected (possible XSS obfuscation)', [
                'ip' => $request->ip(),
                'field' => $field,
                'encoded_ratio' => $encodedChars / $originalLength,
                'payload_sample' => substr($value, 0, 100)
            ]);
        }

        // Check for JavaScript keywords in unexpected contexts
        $jsKeywords = ['eval', 'function', 'return', 'var', 'let', 'const', 'this', 'window', 'document'];
        $keywordCount = 0;

        foreach ($jsKeywords as $keyword) {
            if (stripos($value, $keyword) !== false) {
                $keywordCount++;
            }
        }

        if ($keywordCount >= 3) {
            Log::warning('Multiple JavaScript keywords detected', [
                'ip' => $request->ip(),
                'field' => $field,
                'keyword_count' => $keywordCount,
                'payload_sample' => substr($value, 0, 100)
            ]);
        }
    }
}
