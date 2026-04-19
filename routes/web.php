<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\{
    PaymentController,
    RegistrationController,
    ContactController,
    CamperPortalController,
    CoordinatorPortalController
};
use App\Models\Church;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Landing ─────────────────────────────────────────────────────────────
Route::view('/', 'welcome')->name('home');


// ── Registration ────────────────────────────────────────────────────────
Route::prefix('registration')->name('registration.')->group(function () {

    Route::view('/', 'registration.index')->name('index');
    Route::view('/pay-online', 'registration.pay-online')->name('pay-online');
    Route::view('/callback', 'registration.callback')->name('callback');

    Route::controller(RegistrationController::class)->group(function () {
        Route::post('/validate', 'validateCodeWeb')->name('validate-code-web');
        Route::get('/form/{code}', 'form')->name('form');
        Route::post('/submit', 'submitWeb')->name('submit-web');
        Route::get('/success/{code}', 'success')->name('success');
    });

    Route::controller(PaymentController::class)->group(function () {
        Route::post('/pay-online', 'initiateWeb')->name('payment.initiate-web');
    });
});


// ── API (lightweight) ───────────────────────────────────────────────────
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


// ── Coordinator Portal ──────────────────────────────────────────────────
Route::prefix('coordinator-portal')
    ->name('coordinator.portal.')
    ->controller(CoordinatorPortalController::class)
    ->group(function () {

        Route::get('/', 'index')->name('index');
        Route::post('/login', 'login')->name('login');
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::post('/logout', 'logout')->name('logout');

        Route::get('/batch/{batch}/camper/{entry}', 'form')->name('form');
        Route::post('/batch/{batch}/camper/{entry}', 'submitForm')->name('submit');
    });


// ── Camper Portal ───────────────────────────────────────────────────────
Route::prefix('portal')
    ->name('portal.')
    ->controller(CamperPortalController::class)
    ->group(function () {

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


// ── PWA Check-in ────────────────────────────────────────────────────────
Route::view('/checkin/{any?}', 'pwa.checkin')
    ->where('any', '.*')
    ->name('checkin.app');
