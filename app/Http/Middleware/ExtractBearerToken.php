<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * On some cPanel/Apache shared hosts, the Authorization header is stripped
 * before reaching PHP. This middleware restores it from multiple fallback
 * sources and also supports passing the token via X-Api-Token header
 * or ?_token query parameter as a fallback.
 */
class ExtractBearerToken
{
    public function handle(Request $request, Closure $next)
    {
        // Already have it — nothing to do
        if ($request->hasHeader('Authorization') || $request->bearerToken()) {
            return $next($request);
        }

        $token = null;

        // 1. Try REDIRECT_HTTP_AUTHORIZATION (set by some Apache configs)
        if (! empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        // 2. Try apache_request_headers() — works in some CGI modes
        if (! $token && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $token = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        }

        // 3. Try X-Api-Token custom header (PWA fallback)
        if (! $token) {
            $token = $request->header('X-Api-Token');
            if ($token) {
                $token = 'Bearer ' . $token;
            }
        }

        // 4. Try ?_token query parameter (last resort)
        if (! $token && $request->query('_token')) {
            $token = 'Bearer ' . $request->query('_token');
        }

        if ($token) {
            $request->headers->set('Authorization', $token);
            $_SERVER['HTTP_AUTHORIZATION'] = $token;
        }

        return $next($request);
    }
}
