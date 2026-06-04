<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Compress HTTP responses with gzip when the client supports it.
 * Reduces bandwidth by 60-80% for HTML/JSON responses, directly
 * lowering the data transfer counted against the hosting quota.
 */
class GzipResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Don't compress binary/streamed responses, or if already encoded
        if (
            $response instanceof StreamedResponse ||
            $response instanceof BinaryFileResponse ||
            $response->headers->has('Content-Encoding')
        ) {
            return $response;
        }

        // Only compress if client accepts gzip
        if (! str_contains($request->header('Accept-Encoding', ''), 'gzip')) {
            return $response;
        }

        $content = $response->getContent();
        if ($content === false || strlen($content) < 1024) {
            return $response; // Don't compress tiny responses
        }

        $compressed = gzencode($content, 6); // Level 6 = good balance of speed vs size
        if ($compressed === false) {
            return $response;
        }

        $response->setContent($compressed);
        $response->headers->set('Content-Encoding', 'gzip');
        $response->headers->set('Content-Length', (string) strlen($compressed));
        $response->headers->remove('Transfer-Encoding'); // Incompatible with Content-Length

        return $response;
    }
}
