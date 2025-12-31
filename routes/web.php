<?php

use App\Http\Controllers\PlaygroundController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\BudgetController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laragear\WebAuthn\Http\Routes as WebAuthnRoutes;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Models\Notification;

WebAuthnRoutes::register()->withoutMiddleware(VerifyCsrfToken::class);

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/passkey', fn() => Inertia::render('Auth/Passkey'))->name('passkey');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resources(['users' => UserController::class]);

    Route::resource('playgrounds', PlaygroundController::class);
    Route::post('playgrounds/{id}/items', [PlaygroundController::class, 'storeItem'])->name('playgrounds.items.store');
    Route::put('playgrounds/{id}/items/{item_id}', [PlaygroundController::class, 'updateItem'])->name('playgrounds.items.update');
    Route::delete('/playgrounds/items/{pivot}', [PlaygroundController::class, 'destroyItem'])->name('playgrounds.items.destroy');
    Route::get('/playgrounds/{playground}/total', [PlaygroundController::class, 'valueTotal'])->name('playgrounds.total');
    Route::put('/playground-items/{id}/quantity', [PlaygroundController::class, 'updateQuantity']);
    Route::get('playgrounds/{id}/duplicate', [PlaygroundController::class, 'duplicate'])->name('playgrounds.duplicate');

    Route::resource('parts', PartController::class);
    Route::resource('playgrounds.parts', PartController::class);
    Route::put('/playground-items/{id}/quantity', [PlaygroundController::class, 'updateQuantity']);
    Route::put('/playground-items/batch-update', [PlaygroundController::class, 'batchUpdateQuantities']);
    Route::get('playgrounds/{id}/duplicate', [PlaygroundController::class, 'duplicate'])->name('playgrounds.duplicate');

    Route::get('/notifications', function () {
        return Notification::orderByDesc('created_at')->get();
    })->name('notifications.index');

    Route::resource('calendar', CalendarController::class);
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    Route::resource('clients', ClientController::class);

    Route::resource('budgets', BudgetController::class);

    Route::post('contracts', [ContractController::class, 'store'])->name('contracts.store');
    Route::get('contracts/{id}/pdf', [ContractController::class, 'pdf'])->name('contracts.pdf');
});
