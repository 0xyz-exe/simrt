<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RateLimitAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to auth routes
        if (!in_array($request->route()->getName(), ['login', 'register'])) {
            return $next($request);
        }

        $ip = $request->ip();
        $key = 'auth_attempts:' . $ip;
        $maxAttempts = 5;
        $timeWindow = 300; // 5 minutes

        // Get current attempts
        $attempts = Cache::get($key, 0);

        // Check if rate limit exceeded
        if ($attempts >= $maxAttempts) {
            Log::warning('Rate limit exceeded for authentication', [
                'ip' => $ip,
                'attempts' => $attempts,
                'route' => $request->route()->getName(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'error' => 'Terlalu banyak percobaan login. Coba lagi dalam 5 menit.',
                'retry_after' => $timeWindow
            ], 429);
        }

        $response = $next($request);

        // If authentication failed, increment counter
        if ($this->isAuthenticationFailed($request, $response)) {
            Cache::put($key, $attempts + 1, $timeWindow);

            Log::info('Failed authentication attempt', [
                'ip' => $ip,
                'attempts' => $attempts + 1,
                'route' => $request->route()->getName(),
                'email' => $request->input('email'),
            ]);
        } elseif ($response->isSuccessful()) {
            // Clear attempts on successful auth
            Cache::forget($key);
        }

        return $response;
    }

    /**
     * Check if authentication failed
     */
    private function isAuthenticationFailed(Request $request, Response $response): bool
    {
        // Check for redirect with errors (typical Laravel auth failure)
        if ($response->isRedirection()) {
            $errors = session()->get('errors');
            if ($errors && $errors->has('email')) {
                return true;
            }
        }

        // Check for JSON error response
        if ($response->headers->get('content-type') === 'application/json') {
            $content = json_decode($response->getContent(), true);
            if (isset($content['error']) || isset($content['errors'])) {
                return true;
            }
        }

        return false;
    }
}
