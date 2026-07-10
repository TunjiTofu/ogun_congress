<?php

namespace App\Providers;

use App\Listeners\ResetPhotoStatusOnUpload;
use App\Models\BulkRegistrationBatch;
use App\Models\CampMedia;
use App\Models\OfflinePayment;
use App\Models\RegistrationCode;
use App\Models\YoutubeHighlight;
use App\Observers\AdminActivityObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Opcodes\LogViewer\Facades\LogViewer;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $observer = new AdminActivityObserver();

        Event::listen(MediaHasBeenAddedEvent::class, ResetPhotoStatusOnUpload::class);

        // Hook each model event to the observer manually
        // (avoids needing separate observer classes per model)

        OfflinePayment::created(fn ($m) => $observer->offlinePaymentCreated($m));
        OfflinePayment::updated(fn ($m) => $observer->offlinePaymentUpdated($m));

        RegistrationCode::updated(fn ($m) => $observer->registrationCodeUpdated($m));

        BulkRegistrationBatch::updated(fn ($m) => $observer->bulkBatchUpdated($m));

        // Media role actions
        CampMedia::created(fn ($m) => $observer->campMediaCreated($m));
        CampMedia::updated(fn ($m) => $observer->campMediaUpdated($m));
        CampMedia::deleted(fn ($m) => $observer->campMediaDeleted($m));

        YoutubeHighlight::created(fn ($m) => $observer->youtubeHighlightCreated($m));
        YoutubeHighlight::updated(fn ($m) => $observer->youtubeHighlightUpdated($m));
        YoutubeHighlight::deleted(fn ($m) => $observer->youtubeHighlightDeleted($m));

        // Filament also fires model events for admin panel actions,
        // so the above hooks capture: offline payment creation/confirmation/rejection,
        // code voiding/expiry, bulk batch confirmation — everything an accountant does.

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

        LogViewer::auth(function ($request) {
            return auth()->check() && auth()->user()->hasRole('super_admin');
        });
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
