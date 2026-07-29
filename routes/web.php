<?php

use App\Http\Controllers\BulkIdCardController;
use App\Http\Controllers\CamperExportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RejectedPhotosExportController;
use App\Http\Controllers\SkillRegistrationExportController;
use App\Models\CampMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Console\Output\BufferedOutput;

// ── Proof image server ────────────────────────────────────────────────────────
Route::get('/proof-image/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . base64_decode($path));
    if (!file_exists($fullPath)) abort(404);
    return response()->file($fullPath, [
        'Content-Type' => mime_content_type($fullPath) ?: 'image/jpeg',
        'Cache-Control' => 'private, max-age=3600',
    ]);
})->where('path', '[A-Za-z0-9+/=]+')->middleware('auth')->name('proof.image');

// ── Camper photo server ───────────────────────────────────────────────────────
// Redirects to the direct public storage URL so Apache serves the image as a
// static file — no PHP bootstrap per image. 301 + long cache = browser never
// hits PHP again for the same photo. Eliminates ~875 PHP processes/page-load
// at scale (35 admins × 25 campers/page).
Route::get('/camper-photo/{camper}', function (\App\Models\Camper $camper) {
    $media = $camper->getFirstMedia('photo');
    if (!$media) abort(404);

    // Prefer thumb (smaller file), fall back to original
    $url = $media->hasGeneratedConversion('thumb')
        ? $media->getUrl('thumb')
        : $media->getUrl();

    if ($url) {
        return redirect($url, 301)
            ->header('Cache-Control', 'public, max-age=604800');
    }

    // Fallback: serve file directly (hits only if storage URL is unavailable)
    $path = $media->hasGeneratedConversion('thumb')
        ? $media->getPath('thumb')
        : $media->getPath();

    if (!$path || !file_exists($path)) abort(404);

    return response()->file($path, [
        'Content-Type' => $media->mime_type ?: mime_content_type($path) ?: 'image/jpeg',
        'Cache-Control' => 'public, max-age=604800, immutable',
    ]);
})->where('camper', '[0-9]+')->name('camper.photo');

// ── Storage fallback (no symlink needed) ─────────────────────────────────────
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) abort(404);
    return response()->file($fullPath, [
        'Content-Type' => mime_content_type($fullPath) ?: 'application/octet-stream',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.serve');

// ── Camper QR verification — secretariat & security only ─────────────────────
Route::get('/verify/{camper_number}', function (string $camper_number) {
    if (!auth()->check()) {
        return redirect('/admin/login?next=' . urlencode(request()->url()));
    }

    if (!auth()->user()->hasAnyRole(['secretariat', 'security', 'super_admin'])) {
        abort(403, 'You do not have permission to verify campers.');
    }

    $camper = \App\Models\Camper::with(['church.district', 'health', 'contacts'])
        ->where('camper_number', $camper_number)
        ->firstOrFail();

    return view('verify.camper', compact('camper'));
})->name('camper.verify');

// ── Landing page ──────────────────────────────────────────────────────────────
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/old', fn() => view('welcome2'))->name('home2');

// Global named 'login' route — required by Laravel's auth middleware
Route::get('/login', fn() => redirect('/admin/login'))->name('login');

// ── Registration ──────────────────────────────────────────────────────────────
Route::prefix('registration')->name('registration.')->group(function () {
    Route::get('/', fn() => view('registration.index'))->name('index');
    Route::post('/validate', [RegistrationController::class, 'validateCodeWeb'])->name('validate-code-web');
    Route::get('/pay-online', fn() => view('registration.pay-online'))->name('pay-online');
    Route::post('/pay-online', [PaymentController::class, 'initiateWeb'])->name('payment.initiate-web');
    Route::get('/callback', fn() => view('registration.callback'))->name('callback');
    Route::get('/form/{code}', [RegistrationController::class, 'form'])->name('form');
    Route::post('/submit', [RegistrationController::class, 'submitWeb'])->name('submit-web');
    Route::get('/success/{code}', [RegistrationController::class, 'success'])->name('success');
});

// ── Churches API for cascading dropdown ──────────────────────────────────────
Route::get('/api/churches', function () {
    return \App\Models\Church::where('district_id', request('district_id'))
        ->orderBy('name')->get(['id', 'name']);
});

// ── Contact form ──────────────────────────────────────────────────────────────
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

// ── Batch Paystack payment callback ──────────────────────────────────────────
Route::get('/batch-payment/callback/{batch}', [
    App\Http\Controllers\BatchPaymentController::class, 'callback'
])->name('batch.payment.callback');

// ── Coordinator portal ────────────────────────────────────────────────────────
Route::prefix('coordinator-portal')->name('coordinator.portal.')->group(function () {
    Route::get('/', [App\Http\Controllers\CoordinatorPortalController::class, 'index'])->name('index');
    Route::post('/login', [App\Http\Controllers\CoordinatorPortalController::class, 'login'])->name('do-login');
    Route::get('/dashboard', [App\Http\Controllers\CoordinatorPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [App\Http\Controllers\CoordinatorPortalController::class, 'logout'])->name('logout');
    Route::get('/logout', function () {
        auth()->logout();
        session()->forget('coordinator_logged_in');
        return redirect()->route('coordinator.portal.index');
    });
    Route::get('/batch/{batch}/camper/{entry}', [App\Http\Controllers\CoordinatorPortalController::class, 'form'])->name('form');
    Route::post('/batch/{batch}/camper/{entry}', [App\Http\Controllers\CoordinatorPortalController::class, 'submitForm'])->name('submit');
});

// ── Camper self-service portal ────────────────────────────────────────────────
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [App\Http\Controllers\CamperPortalController::class, 'index'])->name('index');
    Route::post('/login', [App\Http\Controllers\CamperPortalController::class, 'login'])->name('login');
    Route::get('/dashboard', [App\Http\Controllers\CamperPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [App\Http\Controllers\CamperPortalController::class, 'logout'])->name('logout');
});

// ── Document download ─────────────────────────────────────────────────────────
Route::get('/documents/download/{path}', function (string $path) {
    $filePath = base64_decode($path);
    if (!\Illuminate\Support\Facades\Storage::disk('private')->exists($filePath)) {
        abort(404, 'Document not found.');
    }
    return response()->file(storage_path('app/private/' . $filePath), [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
    ]);
})->name('documents.download');

// ── Album page ────────────────────────────────────────────────────────────────
Route::get('/album', function () {
    if (setting('media_upload_enabled', '0') !== '1') {
        abort(404);
    }
    $media = CampMedia::where('status', 'approved')
        ->with('district')
        ->orderBy('district_id')
        ->orderByDesc('created_at')
        ->get()
        ->groupBy('district.name');
    return view('album', compact('media'));
})->name('album.index');

// ── PWA Check-in app ──────────────────────────────────────────────────────────
Route::get('/checkin/{any?}', fn() => view('pwa.checkin'))
    ->where('any', '.*')
    ->name('checkin.app');

Route::get('/checkin/manifest.json', function () {
    return response()->file(public_path('checkin-manifest.json'), [
        'Content-Type' => 'application/manifest+json',
    ]);
});

// ── Attendance exports (auth protected) ──────────────────────────────────────
Route::middleware(['auth'])->prefix('attendance')->name('attendance.')->group(function () {
    Route::get('session/{session}/export', [App\Http\Controllers\AttendanceController::class, 'exportSession'])
        ->name('export.session');
    Route::get('export-all', [App\Http\Controllers\AttendanceController::class, 'exportAll'])
        ->name('export.all');
    Route::get('daily-checkins', [App\Http\Controllers\AttendanceController::class, 'dailyCheckins'])
        ->name('daily.checkins');
});

// ── Exports (auth protected) ──────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('exports')->name('exports.')->group(function () {
    Route::get('id-cards', [BulkIdCardController::class, 'export'])->name('id-cards');
    Route::get('id-cards/status', [BulkIdCardController::class, 'status'])->name('id-cards.status');
    Route::get('id-cards/download/{key}', [BulkIdCardController::class, 'download'])->name('id-cards.download');
    Route::get('campers', [CamperExportController::class, 'export'])->name('campers');
    Route::get('management-report', [App\Http\Controllers\DashboardExportController::class, 'export'])
        ->name('management-report');
    Route::get('rejected-photos', [RejectedPhotosExportController::class, 'export'])  // ← fixed
    ->name('rejected-photos');
    Route::get('skill-registrations', [SkillRegistrationExportController::class, 'export'])
        ->name('skill-registrations');
});

// ── Registration downloads API (polled by success page) ──────────────────────
Route::get('/api/v1/registration/downloads/{identifier}', [RegistrationController::class, 'downloads'])
    ->name('registration.downloads');

// Skill Acquisition Portal (public, session-based auth)
Route::prefix('skills')->name('skills.')->group(function () {
    Route::get('/',        [App\Http\Controllers\SkillController::class, 'index'])   ->name('index');
    Route::post('/login',  [App\Http\Controllers\SkillController::class, 'login'])   ->name('login');
    Route::get('/dashboard', [App\Http\Controllers\SkillController::class, 'dashboard'])->name('dashboard');
    Route::post('/register', [App\Http\Controllers\SkillController::class, 'register'])->name('register');
    Route::post('/logout',  [App\Http\Controllers\SkillController::class, 'logout']) ->name('logout');
});

// ── Admin artisan shortcuts (super_admin only) ────────────────────────────────
Route::middleware(['auth'])->prefix('artisan')->name('artisan.')->group(function () {

    $terminal = function (string $command, string $result) {
        $ok = !str_contains($result, 'ERROR');
        return response(
            '<html><head><title>Artisan</title></head>'
            . '<body style="margin:0;background:#1e1e1e;color:#d4d4d4;font-family:monospace;padding:2rem">'
            . '<p style="color:#94A3B8;margin-bottom:0.5rem">$ <strong style="color:#4ade80">' . e($command) . '</strong></p>'
            . '<pre style="white-space:pre-wrap;margin-top:1rem">' . e($result) . '</pre>'
            . ($ok
                ? '<p style="color:#4ade80;margin-top:1rem">✓ Done</p>'
                : '<p style="color:#f87171;margin-top:1rem">✗ Error</p>')
            . '</body></html>'
        )->header('Content-Type', 'text/html');
    };

    Route::get('migrate', function () use ($terminal) {
        if (!auth()->user()->hasRole('super_admin')) abort(403);
        try {
            $out = new BufferedOutput();
            Artisan::call('migrate', ['--force' => true], $out);
            return $terminal('php artisan migrate --force', $out->fetch());
        } catch (\Throwable $e) {
            return $terminal('php artisan migrate --force', 'ERROR: ' . $e->getMessage());
        }
    })->name('migrate');

    Route::get('migrate/rollback', function () use ($terminal) {
        if (!auth()->user()->hasRole('super_admin')) abort(403);
        $step = max(1, (int)request('step', 1));
        try {
            $out = new BufferedOutput();
            Artisan::call('migrate:rollback', ['--force' => true, '--step' => $step], $out);
            return $terminal("php artisan migrate:rollback --step={$step}", $out->fetch());
        } catch (\Throwable $e) {
            return $terminal("php artisan migrate:rollback --step={$step}", 'ERROR: ' . $e->getMessage());
        }
    })->name('migrate.rollback');

    Route::get('seed', function () use ($terminal) {
        if (!auth()->user()->hasRole('super_admin')) abort(403);

        $allowed = [
            'RolesSeeder',
            'DatabaseSeeder',
            'DistrictAndChurchSeeder',
            'CampRoleSeeder',
            'SkillsSeeder',
        ];

        $class = request('class', 'RolesAndPermissionsSeeder');

        if (!in_array($class, $allowed)) {
            return $terminal("php artisan db:seed --class={$class}", "ERROR: '{$class}' is not in the allowed seeders list.\n\nAllowed: " . implode(', ', $allowed));
        }

        try {
            $out = new BufferedOutput();
            Artisan::call('db:seed', ['--class' => $class, '--force' => true], $out);
            return $terminal("php artisan db:seed --class={$class} --force", $out->fetch());
        } catch (\Throwable $e) {
            return $terminal("php artisan db:seed --class={$class}", 'ERROR: ' . $e->getMessage());
        }
    })->name('seed');

    Route::get('optimize-clear', function () use ($terminal) {
        if (!auth()->user()->hasRole('super_admin')) abort(403);
        try {
            $out = new BufferedOutput();
            Artisan::call('optimize:clear', [], $out);
            return $terminal('php artisan optimize:clear', $out->fetch());
        } catch (\Throwable $e) {
            return $terminal('php artisan optimize:clear', 'ERROR: ' . $e->getMessage());
        }
    })->name('optimize.clear');

});

// ── Test email (dev only) ─────────────────────────────────────────────────────
Route::get('/test-email', function (Request $request) {
    $user = (object)[
        'name' => 'John Doe',
        'email' => 'johnd@gmail.com',
    ];
    return view('emails.admin-welcome', [
        'user' => $user,
        'plainPassword' => 'TempPassword@123',
        'roleName' => 'Super Admin',
        'adminUrl' => url('/admin'),
        'landingUrl' => url('/'),
    ]);
});
