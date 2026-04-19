<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\{
    PaymentController,
    RegistrationController,
    ContactController,
    CamperPortalController
};
use App\Models\Church;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Landing ─────────────────────────────────────────────────────────────
Route::view('/', 'welcome')->name('home');


// ── Registration Module ─────────────────────────────────────────────────
Route::prefix('registration')->name('registration.')->group(function () {

    Route::view('/', 'registration.index')->name('index');

    Route::controller(RegistrationController::class)->group(function () {
        Route::post('/validate', 'validateCodeWeb')->name('validate-code-web');
        Route::get('/form/{code}', 'form')->name('form');
        Route::post('/submit', 'submitWeb')->name('submit-web');
        Route::get('/success/{code}', 'success')->name('success');
    });

    Route::controller(PaymentController::class)->group(function () {
        Route::view('/pay-online', 'registration.pay-online')->name('pay-online');
        Route::post('/pay-online', 'initiateWeb')->name('payment.initiate-web');
    });

    Route::view('/callback', 'registration.callback')->name('callback');
});


// ── Lightweight APIs ────────────────────────────────────────────────────
Route::prefix('api')->group(function () {

    Route::get('/churches', function () {
        return Church::query()
            ->where('district_id', request('district_id'))
            ->orderBy('name')
            ->get(['id', 'name']);
    });
});


// ── Contact ─────────────────────────────────────────────────────────────
Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');


// ── Camper Portal ───────────────────────────────────────────────────────
Route::prefix('portal')->name('portal.')->controller(CamperPortalController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/login', 'login')->name('login');
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', 'logout')->name('logout');
});


// ── Documents ───────────────────────────────────────────────────────────
Route::get('/documents/download/{path}', function (string $path) {
    $filePath = base64_decode($path);

    abort_unless(
        Storage::disk('private')->exists($filePath),
        404,
        'Document not found.'
    );

    return response()->file(
        storage_path("app/private/{$filePath}"),
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
        ]
    );
})->name('documents.download');


// ── PWA Check-in App ────────────────────────────────────────────────────
Route::view('/checkin/{any?}', 'pwa.checkin')
    ->where('any', '.*')
    ->name('checkin.app');
