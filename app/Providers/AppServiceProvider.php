<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force PHP to use user-writable tmp directory (shared hosting fix)
        putenv('TMPDIR=/home2/gratusco/tmp');
        ini_set('upload_tmp_dir', '/home2/gratusco/tmp');

        Model::preventLazyLoading(! app()->isProduction());
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());

        Password::defaults(function () {
            return app()->isProduction()
                ? Password::min(8)->mixedCase()->numbers()
                : Password::min(6);
        });

        $this->configureRateLimiters();
    }

    private function configureRateLimiters(): void
    {
        RateLimiter::for('payment_initiate', function (Request $request) {
            $limit = config('camp.rate_limits.payment_initiate');
            return Limit::perMinutes($limit['decay_minutes'], $limit['attempts'])
                ->by($request->ip())
                ->response(fn () => response()->json([
                    'success' => false,
                    'message' => 'Too many payment attempts. Please wait a few minutes and try again.',
                ], 429));
        });

        RateLimiter::for('code_validate', function (Request $request) {
            $limit = config('camp.rate_limits.code_validate');
            return Limit::perMinutes($limit['decay_minutes'], $limit['attempts'])
                ->by($request->ip())
                ->response(fn () => response()->json([
                    'success' => false,
                    'message' => 'Too many attempts. Please wait 15 minutes before trying again.',
                ], 429));
        });

        RateLimiter::for('checkin_api', function (Request $request) {
            $limit = config('camp.rate_limits.checkin_api');
            return Limit::perMinutes($limit['decay_minutes'], $limit['attempts'])
                ->by($request->bearerToken() ?? $request->ip());
        });
    }
}
