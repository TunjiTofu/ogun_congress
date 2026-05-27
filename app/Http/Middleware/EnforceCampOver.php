<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforceCampOver
{
    public function handle(Request $request, Closure $next)
    {
        // Force-logout any currently-logged-in non-super_admin when camp is over.
        // Login blocking is handled by App\Filament\Auth\Login::authenticate().
        if (setting('camp_over', '0') === '1') {
            $user = auth()->user();

            if ($user && ! $user->hasRole('super_admin')) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/admin/login')->with('camp_over', true);
            }
        }

        return $next($request);
    }
}
