<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! $user || ! $user->must_change_password) {
            return $next($request);
        }

        $changeUrl = url('/admin/change-password-required');

        // Always allow: the change page itself, logout, Livewire calls originating from it
        $isChangePage = $request->is('admin/change-password-required');
        $isLogout     = $request->is('admin/logout');
        $isLivewire   = $request->is('livewire/update') &&
            str_contains($request->header('Referer', ''), 'change-password-required');

        if ($isChangePage || $isLogout || $isLivewire) {
            return $next($request);
        }

        // Block all other admin pages
        if ($request->is('admin/*')) {
            return redirect($changeUrl);
        }

        return $next($request);
    }
}
