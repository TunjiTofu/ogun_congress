<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;


// ── Proof image server (payment teller uploads) ──────────────────────────────
Route::get('/proof-image/{path}', function (string $path) {
    $relativePath = base64_decode($path);
    $fullPath = storage_path('app/public/' . $relativePath);

    if (! file_exists($fullPath)) {
        abort(404);
    }

    $mimeType = mime_content_type($fullPath) ?: 'image/jpeg';
    return response()->file($fullPath, [
        'Content-Type'  => $mimeType,
        'Cache-Control' => 'private, max-age=3600',
    ]);
})->where('path', '[A-Za-z0-9+/=]+')->middleware('auth')->name('proof.image');

// ── Camper photo server (serves from Spatie MediaLibrary disk path) ──────────────
// This bypasses symlink and URL issues by reading the file directly from disk.
Route::get('/camper-photo/{camper}', function (\App\Models\Camper $camper) {
    $media = $camper->getFirstMedia('photo');

    if (! $media) {
        abort(404);
    }

    // Try thumb first, fall back to original
    $path = null;
    if ($media->hasGeneratedConversion('thumb')) {
        $thumbPath = $media->getPath('thumb');
        if (file_exists($thumbPath)) {
            $path = $thumbPath;
        }
    }
    if (! $path) {
        $originalPath = $media->getPath();
        if (file_exists($originalPath)) {
            $path = $originalPath;
        }
    }

    if (! $path) {
        abort(404);
    }

    $mimeType = $media->mime_type ?: mime_content_type($path) ?: 'image/jpeg';

    return response()->file($path, [
        'Content-Type'  => $mimeType,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('camper', '[0-9]+')->name('camper.photo');

// ── Permanent storage file server ─────────────────────────────────────────────
// Serves files from storage/app/public WITHOUT requiring `storage:link` symlink.
// This route intercepts /storage/* requests and streams them directly.
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);

    if (! file_exists($fullPath)) {
        abort(404);
    }

    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

    return response()->file($fullPath, [
        'Content-Type'  => $mimeType,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.serve');

// ── Camper QR verification (public — scanned from ID card) ──────────────────────
Route::get('/verify/{camper_number}', function (string $camper_number) {
    $camper = \App\Models\Camper::with(['church.district', 'health', 'contacts'])
        ->where('camper_number', $camper_number)
        ->firstOrFail();

    return view('verify.camper', compact('camper'));
})->name('camper.verify');

// ── Landing page ───────────────────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'))->name('home');

// ── Registration ───────────────────────────────────────────────────────────────
Route::prefix('registration')->name('registration.')->group(function () {

    // Code entry
    Route::get('/', fn () => view('registration.index'))->name('index');

    // Validate code and redirect to form (web form POST)
    Route::post('/validate', [RegistrationController::class, 'validateCodeWeb'])->name('validate-code-web');

    // Online payment form
    Route::get('/pay-online', fn () => view('registration.pay-online'))->name('pay-online');
    Route::post('/pay-online', [PaymentController::class, 'initiateWeb'])->name('payment.initiate-web');

    // Paystack callback page
    Route::get('/callback', fn () => view('registration.callback'))->name('callback');

    // Registration wizard (requires valid code passed as route param)
    Route::get('/form/{code}', [RegistrationController::class, 'form'])->name('form');

    // Form submission
    Route::post('/submit', [RegistrationController::class, 'submitWeb'])->name('submit-web');

    // Success / download page
    Route::get('/success/{code}', [RegistrationController::class, 'success'])->name('success');
});

// ── Churches API for cascading dropdown ───────────────────────────────────────
Route::get('/api/churches', function () {
    $districtId = request('district_id');
    return \App\Models\Church::where('district_id', $districtId)
        ->orderBy('name')
        ->get(['id', 'name']);
});

// ── Contact form submission ────────────────────────────────────────────────────
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])
    ->name('contact.store');

// ── Coordinator portal — fill camper forms after batch confirmation ──────────────
Route::prefix('coordinator-portal')->name('coordinator.portal.')->group(function () {
    Route::get('/',                       [App\Http\Controllers\CoordinatorPortalController::class, 'index'])->name('index');
    Route::post('/login',                 [App\Http\Controllers\CoordinatorPortalController::class, 'login'])->name('login');
    Route::get('/dashboard',              [App\Http\Controllers\CoordinatorPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout',                [App\Http\Controllers\CoordinatorPortalController::class, 'logout'])->name('logout');
    Route::get('/batch/{batch}/camper/{entry}',   [App\Http\Controllers\CoordinatorPortalController::class, 'form'])->name('form');
    Route::post('/batch/{batch}/camper/{entry}',  [App\Http\Controllers\CoordinatorPortalController::class, 'submitForm'])->name('submit');
});

// ── Camper Self-Service Portal ─────────────────────────────────────────────────
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/',        [App\Http\Controllers\CamperPortalController::class, 'index'])->name('index');
    Route::post('/login',  [App\Http\Controllers\CamperPortalController::class, 'login'])->name('login');
    Route::get('/dashboard', [App\Http\Controllers\CamperPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [App\Http\Controllers\CamperPortalController::class, 'logout'])->name('logout');
});
Route::get('/documents/download/{path}', function (string $path) {
    $filePath = base64_decode($path);

    if (! \Illuminate\Support\Facades\Storage::disk('private')->exists($filePath)) {
        abort(404, 'Document not found.');
    }

    $fullPath = storage_path('app/private/' . $filePath);
    $filename = basename($filePath);

    return response()->file($fullPath, [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => "inline; filename=\"{$filename}\"",
    ]);
})->name('documents.download');
Route::get('/checkin/{any?}', fn () => view('pwa.checkin'))
    ->where('any', '.*')
    ->name('checkin.app');

// ── Batch Paystack payment callback ───────────────────────────────────────────
Route::get('/batch-payment/callback/{batch}', [
    App\Http\Controllers\BatchPaymentController::class, 'callback'
])->name('batch.payment.callback');

// ── Coordinator portal ─────────────────────────────────────────────────────────
Route::prefix('coordinator-portal')->name('coordinator.portal.')->group(function () {
    Route::get('/',                                        [App\Http\Controllers\CoordinatorPortalController::class, 'index'])->name('index');
    Route::post('/login',                                  [App\Http\Controllers\CoordinatorPortalController::class, 'login'])->name('login');
    Route::get('/dashboard',                               [App\Http\Controllers\CoordinatorPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout',                                 [App\Http\Controllers\CoordinatorPortalController::class, 'logout'])->name('logout');
    // Also handle GET logout (e.g. from direct links) — just redirect to login
    Route::get('/logout', function () {
        auth()->logout();
        session()->forget('coordinator_logged_in');
        return redirect()->route('coordinator.portal.index')->with('success', 'You have been logged out.');
    });
    Route::get('/batch/{batch}/camper/{entry}',            [App\Http\Controllers\CoordinatorPortalController::class, 'form'])->name('form');
    Route::post('/batch/{batch}/camper/{entry}',           [App\Http\Controllers\CoordinatorPortalController::class, 'submitForm'])->name('submit');
});

// ── Contact form ───────────────────────────────────────────────────────────────
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');


// PWA Check-in app (requires auth — secretariat/security)
Route::middleware(['auth'])->prefix('checkin')->group(function () {
    Route::get('/', [App\Http\Controllers\CheckinController::class, 'index'])->name('checkin.app');
    Route::get('/manifest.json', function () {
        return response()->file(public_path('checkin-manifest.json'), [
            'Content-Type' => 'application/manifest+json',
        ]);
    });
});

// Add these to routes/web.php (inside auth middleware group):

Route::middleware(['auth'])->prefix('attendance')->name('attendance.')->group(function () {
    Route::get('session/{session}/export', [App\Http\Controllers\AttendanceController::class, 'exportSession'])
        ->name('export.session');
    Route::get('export-all', [App\Http\Controllers\AttendanceController::class, 'exportAll'])
        ->name('export.all');
    Route::get('daily-checkins', [App\Http\Controllers\AttendanceController::class, 'dailyCheckins'])
        ->name('daily.checkins');
});
