<?php

use App\Http\Controllers\CheckinController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/dev-route.php';

// ── Public webhook (no auth, no CSRF — excluded in bootstrap/app.php) ─────────
Route::post('/webhooks/paystack', [PaymentController::class, 'webhook'])
    ->name('webhooks.paystack');

// ── Public API (rate-limited, no auth) ────────────────────────────────────────
Route::prefix('v1')->name('api.')->group(function () {

    // Payment
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::post('initiate', [PaymentController::class, 'initiate'])
            ->middleware('throttle:payment_initiate')
            ->name('initiate');

        Route::get('status/{code}', [PaymentController::class, 'status'])
            ->name('status');
    });

    // Registration
    Route::prefix('registration')->name('registration.')->group(function () {
        Route::post('validate-code', [RegistrationController::class, 'validateCode'])
            ->middleware('throttle:code_validate')
            ->name('validate-code');

        Route::post('submit', [RegistrationController::class, 'submit'])
            ->name('submit');

        Route::get('downloads/{code}', [RegistrationController::class, 'downloads'])
            ->name('downloads');
    });
});

// ── Offline check-in PWA API (Sanctum token auth) ─────────────────────────────
Route::prefix('checkin')->name('checkin.')->group(function () {

    // Auth — no token required
//    Route::post('auth', [CheckinController::class, 'auth'])
//        ->name('auth');

//    // Protected endpoints — require valid Sanctum token with [checkin] ability
//    Route::middleware(['auth:sanctum', 'ability:checkin'])->group(function () {
//        Route::get('sync', [CheckinController::class, 'sync'])
//            ->middleware('throttle:checkin_api')
//            ->name('sync');
//
//        Route::post('events', [CheckinController::class, 'storeEvents'])
//            ->name('events');
//
//        Route::get('camper/{code}', [CheckinController::class, 'camper'])
//            ->middleware('throttle:checkin_api')
//            ->name('camper');
//    });
});

// Auth endpoint — no auth middleware (this IS the login)
Route::post('checkin/auth', [App\Http\Controllers\CheckinController::class, 'auth'])
    ->name('checkin.auth');

// Protected endpoints — require Sanctum token
Route::middleware('auth:sanctum')->prefix('checkin')->group(function () {
    Route::get('sessions', [App\Http\Controllers\CheckinController::class, 'sessions'])->name('checkin.sessions');
    Route::get('sync',                [App\Http\Controllers\CheckinController::class, 'sync'])->name('checkin.sync');
    Route::get('camper/{identifier}', [App\Http\Controllers\CheckinController::class, 'lookup'])->name('checkin.lookup');
    Route::post('events',             [App\Http\Controllers\CheckinController::class, 'storeEvents'])->name('checkin.events');
});


// Add to routes/api.php (inside auth:sanctum):
Route::middleware('auth:sanctum')->group(function () {
    // Return active sessions for PWA attendance mode
    Route::get('programme-sessions', function () {
        $sessions = \App\Models\ProgrammeSession::where('is_active', true)
            ->whereDate('date', today())
            ->orderBy('start_time')
            ->get(['id', 'title', 'date', 'start_time', 'end_time', 'venue']);
        return response()->json($sessions);
    })->name('api.programme.sessions');
});
