<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DDoSProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // DDoS Protection Parameters
        $maxRequests = 100;        // Max requests per window
        $timeWindow = 60;          // Time window in seconds
        $banDuration = 300;        // Ban duration in seconds (5 minutes)

        // Keys for tracking
        $requestKey = "ddos_requests:{$ip}";
        $banKey = "ddos_banned:{$ip}";

        // Check if IP is currently banned
        if (Cache::has($banKey)) {
            Log::warning('Blocked DDoS attack attempt', [
                'ip' => $ip,
                'ban_remaining' => Cache::get($banKey)
            ]);

            return response()->json([
                'error' => 'Rate limit exceeded. Access temporarily blocked.',
                'retry_after' => Cache::get($banKey)
            ], 429);
        }

        // Get current request count
        $requests = Cache::get($requestKey, 0);

        // Check if limit exceeded
        if ($requests >= $maxRequests) {
            // Ban the IP
            Cache::put($banKey, time() + $banDuration, $banDuration);
            Cache::forget($requestKey);

            Log::critical('DDoS attack detected - IP banned', [
                'ip' => $ip,
                'requests' => $requests,
                'ban_duration' => $banDuration,
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);

            return response()->json([
                'error' => 'DDoS protection activated. IP has been temporarily banned.',
                'ban_duration' => $banDuration
            ], 429);
        }

        // Increment request counter
        if (Cache::has($requestKey)) {
            Cache::increment($requestKey);
        } else {
            Cache::put($requestKey, 1, $timeWindow);
        }

        $response = $next($request);

        // Add rate limit headers
        $remaining = max(0, $maxRequests - ($requests + 1));
        $response->headers->set('X-RateLimit-Limit', $maxRequests);
        $response->headers->set('X-RateLimit-Remaining', $remaining);
        $response->headers->set('X-RateLimit-Reset', time() + $timeWindow);

        return $response;
    }
}
