<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// ── Proof image server ────────────────────────────────────────────────────────
Route::get('/proof-image/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . base64_decode($path));
    if (! file_exists($fullPath)) abort(404);
    return response()->file($fullPath, [
        'Content-Type'  => mime_content_type($fullPath) ?: 'image/jpeg',
        'Cache-Control' => 'private, max-age=3600',
    ]);
})->where('path', '[A-Za-z0-9+/=]+')->middleware('auth')->name('proof.image');

// ── Camper photo server ───────────────────────────────────────────────────────
Route::get('/camper-photo/{camper}', function (\App\Models\Camper $camper) {
    $media = $camper->getFirstMedia('photo');
    if (! $media) abort(404);

    $path = null;
    if ($media->hasGeneratedConversion('thumb')) {
        $t = $media->getPath('thumb');
        if (file_exists($t)) $path = $t;
    }
    if (! $path) {
        $o = $media->getPath();
        if (file_exists($o)) $path = $o;
    }
    if (! $path) abort(404);

    return response()->file($path, [
        'Content-Type'  => $media->mime_type ?: mime_content_type($path) ?: 'image/jpeg',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('camper', '[0-9]+')->name('camper.photo');

// ── Storage fallback (no symlink needed) ──────────────────────────────────────
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);
    if (! file_exists($fullPath)) abort(404);
    return response()->file($fullPath, [
        'Content-Type'  => mime_content_type($fullPath) ?: 'application/octet-stream',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.serve');

// ── Camper QR verification — secretariat & security only ─────────────────────
Route::get('/verify/{camper_number}', function (string $camper_number) {
    // Check Filament/admin session (not the default web guard)
    if (! auth()->check()) {
        return redirect(route('filament.admin.auth.login') . '?next=' . urlencode(request()->url()));
    }

    if (! auth()->user()->hasAnyRole(['secretariat', 'security', 'super_admin'])) {
        abort(403, 'You do not have permission to verify campers.');
    }

    $camper = \App\Models\Camper::with(['church.district', 'health', 'contacts'])
        ->where('camper_number', $camper_number)
        ->firstOrFail();

    return view('verify.camper', compact('camper'));
})->name('camper.verify');

// ── Landing page ──────────────────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'))->name('home');

// ── Registration ──────────────────────────────────────────────────────────────
Route::prefix('registration')->name('registration.')->group(function () {
    Route::get('/', fn () => view('registration.index'))->name('index');
    Route::post('/validate', [RegistrationController::class, 'validateCodeWeb'])->name('validate-code-web');
    Route::get('/pay-online', fn () => view('registration.pay-online'))->name('pay-online');
    Route::post('/pay-online', [PaymentController::class, 'initiateWeb'])->name('payment.initiate-web');
    Route::get('/callback', fn () => view('registration.callback'))->name('callback');
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
    Route::get('/',                             [App\Http\Controllers\CoordinatorPortalController::class, 'index'])->name('index');
    Route::post('/login',                       [App\Http\Controllers\CoordinatorPortalController::class, 'login'])->name('login');
    Route::get('/dashboard',                    [App\Http\Controllers\CoordinatorPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout',                      [App\Http\Controllers\CoordinatorPortalController::class, 'logout'])->name('logout');
    Route::get('/logout', function () {
        auth()->logout();
        session()->forget('coordinator_logged_in');
        return redirect()->route('coordinator.portal.index');
    });
    Route::get('/batch/{batch}/camper/{entry}',  [App\Http\Controllers\CoordinatorPortalController::class, 'form'])->name('form');
    Route::post('/batch/{batch}/camper/{entry}', [App\Http\Controllers\CoordinatorPortalController::class, 'submitForm'])->name('submit');
});

// ── Camper self-service portal ────────────────────────────────────────────────
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/',          [App\Http\Controllers\CamperPortalController::class, 'index'])->name('index');
    Route::post('/login',    [App\Http\Controllers\CamperPortalController::class, 'login'])->name('login');
    Route::get('/dashboard', [App\Http\Controllers\CamperPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout',   [App\Http\Controllers\CamperPortalController::class, 'logout'])->name('logout');
});

// ── Document download ─────────────────────────────────────────────────────────
Route::get('/documents/download/{path}', function (string $path) {
    $filePath = base64_decode($path);
    if (! \Illuminate\Support\Facades\Storage::disk('private')->exists($filePath)) {
        abort(404, 'Document not found.');
    }
    return response()->file(storage_path('app/private/' . $filePath), [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
    ]);
})->name('documents.download');

// ── PWA Check-in app ─────────────────────────────────────────────────────────
// The JS handles its own auth (Sanctum token) — no Laravel middleware needed here
// so the PWA shell loads even before the user "logs in" via the in-app form.
Route::get('/checkin/{any?}', fn () => view('pwa.checkin'))
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
    Route::get('export-all',              [App\Http\Controllers\AttendanceController::class, 'exportAll'])
        ->name('export.all');
    Route::get('daily-checkins',          [App\Http\Controllers\AttendanceController::class, 'dailyCheckins'])
        ->name('daily.checkins');
});
