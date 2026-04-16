<?php

use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// ── Public landing page ────────────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'))->name('home');

// ── Registration ───────────────────────────────────────────────────────────────
Route::prefix('registration')->name('registration.')->group(function () {

    // Code entry form
    Route::get('/', fn () => view('registration.index'))->name('index');

    // Paystack payment callback (polled until code is ACTIVE)
    Route::get('callback', fn () => view('registration.callback'))->name('callback');

    // Step-by-step registration wizard (only accessible with a valid code)
    Route::get('form/{code}', fn ($code) => view('registration.form', compact('code')))
        ->name('form');

    // Post-registration download page
    Route::get('success/{code}', [RegistrationController::class, 'success'])
        ->name('success');
});

// ── Offline check-in PWA shell ────────────────────────────────────────────────
Route::get('/checkin/{any?}', fn () => view('pwa.checkin'))
    ->where('any', '.*')
    ->name('checkin.app');
