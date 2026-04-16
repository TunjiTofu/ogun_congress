<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

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

// ── Offline check-in PWA shell ─────────────────────────────────────────────────
Route::get('/checkin/{any?}', fn () => view('pwa.checkin'))
    ->where('any', '.*')
    ->name('checkin.app');
