<?php

use App\Http\Middleware\SetAppTimezone;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Exclude Paystack webhook from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'api/webhooks/paystack',
            'api/checkin/auth',
            'api/webhooks/*',
        ]);

        // API middleware group
//        $middleware->api(prepend: [
//            EnsureFrontendRequestsAreStateful::class,
//        ]);

        // Aliases
        $middleware->alias([
            'role'       => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // When an unauthenticated request hits an API endpoint,
        // return a proper 401 JSON instead of crashing on route('login')
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated. Please log in to the check-in app.',
                ], 401);
            }

            // Web requests → Filament login
            return redirect()->route('filament.admin.auth.login');
        });
    })
    ->withProviders([
        App\Providers\AppServiceProvider::class,
        App\Providers\RepositoryServiceProvider::class,
        App\Providers\Filament\AdminPanelProvider::class,
    ])
    ->create();
