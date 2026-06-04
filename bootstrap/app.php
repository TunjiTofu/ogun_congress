<?php

use App\Http\Middleware\EnforceCampOver;
use App\Http\Middleware\ExtractBearerToken;
use App\Http\Middleware\GzipResponse;
use App\Http\Middleware\SetAppTimezone;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__ . '/../routes/web.php',
        api:      __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Exclude Paystack webhook from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'api/webhooks/paystack',
            'api/checkin/auth',
            'api/webhooks/*',
        ]);

        // API middleware group
        $middleware->api(prepend: [
//            EnsureFrontendRequestsAreStateful::class,
            ExtractBearerToken::class,
        ]);

        // Aliases
        $middleware->alias([
            'role'               => RoleMiddleware::class,
            'permission'         => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        // Gzip compress web responses — reduces bandwidth by 60-80%
        $middleware->appendToGroup('web', GzipResponse::class);

        // Force-logout non-super_admin when camp is over
        $middleware->appendToGroup('web', EnforceCampOver::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // ── Unauthenticated ───────────────────────────────────────────────────
        // API → 401 JSON. Web → /admin/login (never route('login')).
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated. Please log in to the check-in app.',
                ], 401);
            }

            return redirect('/admin/login');
        });

        // ── 404 Not Found ─────────────────────────────────────────────────────
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Not found.'], 404);
            }

            return response()->view('errors.404', [], 404);
        });

        // ── 403 Access Denied ─────────────────────────────────────────────────
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }

            return response()->view('errors.403', [], 403);
        });

        // ── All other exceptions ──────────────────────────────────────────────
        // Log silently; never show raw stack traces to the user.
        $exceptions->render(function (\Throwable $e, Request $request) {
            // API / JSON clients always get a structured response
            if ($request->expectsJson() || $request->is('api/*')) {
                Log::error('api.unhandled_exception', [
                    'message'   => $e->getMessage(),
                    'exception' => get_class($e),
                    'url'       => $request->fullUrl(),
                    'trace'     => $e->getTraceAsString(),
                ]);
                return response()->json([
                    'message' => 'Something went wrong. Please try again.',
                ], 500);
            }

            // Filament admin / Livewire — log but let Filament render its own error UI
            if ($request->is('admin/*') || $request->is('livewire/*')) {
                Log::error('admin.unhandled_exception', [
                    'message'   => $e->getMessage(),
                    'exception' => get_class($e),
                    'url'       => $request->fullUrl(),
                    'user_id'   => auth()->id(),
                    'trace'     => $e->getTraceAsString(),
                ]);
                // Return null → Laravel falls through to its default handler
                // (Filament has its own error page for admin routes)
                return null;
            }

            // Public web pages — log and show branded error page
            Log::error('web.unhandled_exception', [
                'message'   => $e->getMessage(),
                'exception' => get_class($e),
                'url'       => $request->fullUrl(),
                'trace'     => $e->getTraceAsString(),
            ]);

            return response()->view('errors.500', [
                'message' => 'Something went wrong. Our team has been notified.',
            ], 500);
        });

    })
    ->withProviders([
        App\Providers\AppServiceProvider::class,
        App\Providers\RepositoryServiceProvider::class,
        App\Providers\Filament\AdminPanelProvider::class,
    ])
    ->create();
