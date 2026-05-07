<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        // API / JSON requests → return null → Laravel sends 401 JSON automatically
        // Web requests → redirect to Filament login
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        return route('filament.admin.auth.login');
    }
}
