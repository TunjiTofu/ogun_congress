<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMigrationKey
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->header('X-Migration-Key') || $request->header('X-Migration-Key') != config('migration.key')) {
            return response('You are unauthorized to carry out this action', Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
