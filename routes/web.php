<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Models\Church;

use App\Http\Controllers\{
    PaymentController,
    RegistrationController,
    ContactController,
    CamperPortalController,
    CoordinatorPortalController,
    BatchPaymentController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Storage (Safer version) ─────────────────────────────────────────────
Route::get('/storage/{path}', function (string $path) {

    abort_if(str_contains($path, '..'), 403); // prevent traversal

    $fullPath = storage_path('app/public/' . $path);

    abort_unless(file_exists($fullPath), 404);

    return response()->file($fullPath, [
        'Content-Type'  => mime_content_type($fullPath) ?: 'application/octet-stream',
        'Cache-Control' => 'public, max-age=86400',
    ]);

})->where('path', '.*')->name('storage.serve');


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

    Route::post('/pay-online', [PaymentController::class, 'initiateWeb'])
        ->name('payment.initiate-web');
});


// ── API ─────────────────────────────────────────────────────────────────
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

        // Optional GET logout fallback
        Route::get('/logout', function () {
            auth()->logout();
            session()->forget('coordinator_logged_in');

            return redirect()
                ->route('coordinator.portal.index')
                ->with('success', 'Logged out.');
        });

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


// ── Batch Payment Callback ──────────────────────────────────────────────
Route::get('/batch-payment/callback/{batch}', [BatchPaymentController::class, 'callback'])
    ->name('batch.payment.callback');


// ── Documents (still needs improvement) ─────────────────────────────────
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


// ── PWA ─────────────────────────────────────────────────────────────────
Route::view('/checkin/{any?}', 'pwa.checkin')
    ->where('any', '.*')
    ->name('checkin.app');
