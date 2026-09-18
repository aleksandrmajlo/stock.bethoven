<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KitController;
use App\Http\Controllers\Admin\KitPartController;
use App\Http\Controllers\Admin\EquipmentSettingController;
use App\Http\Controllers\Admin\RepairOrderController;

/*
|--------------------------------------------------------------------------
| Log Viewer
|--------------------------------------------------------------------------
 */
Route::get('logs', function () {
    return redirect('/log-viewer');
})->name('logs');

/*
|--------------------------------------------------------------------------
| Main route
|--------------------------------------------------------------------------
|
| http://localhost/
| Если пользователь НЕ авторизован:
| -> login form
|
| Если авторизован:
| -> admin dashboard
|
*/
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    return redirect()->route('admin.dashboard');
});


/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Kits CRUD
        |--------------------------------------------------------------------------
        */
        Route::get('kits/list', [KitController::class, 'list'])
            ->name('kits.list');
        Route::resource('kits', KitController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        /*
        |--------------------------------------------------------------------------
        | Kit Parts CRUD
        |--------------------------------------------------------------------------
        */
        Route::get('kit-parts/list', [KitPartController::class, 'list'])
            ->name('kit-parts.list');
        Route::resource('kit-parts', KitPartController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::get('kit-parts/next-barcode', [KitPartController::class, 'nextBarcode'])
            ->name('kit-parts.next-barcode');

        Route::get('receiving', [KitPartController::class, 'receiving'])
            ->name('receiving.index');

        Route::post('receiving/scan', [KitPartController::class, 'scan'])
            ->name('receiving.scan');

        Route::post('receiving/accept', [KitPartController::class, 'accept'])
            ->name('receiving.accept');

        /*
         * Ordes
         */
        Route::get('repair-orders/list', [RepairOrderController::class, 'list'])
            ->name('repair-orders.list');
        Route::resource('repair-orders', RepairOrderController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::post('repair-orders/{repairOrder}/scan', [RepairOrderController::class, 'scan'])
            ->name('repair-orders.scan');
        Route::post('repair-orders/{repairOrder}/confirm-payment', [RepairOrderController::class, 'confirmPayment'])
            ->name('repair-orders.confirm-payment');

        /*
         * Setting
         */
        Route::get('equipment', [EquipmentSettingController::class, 'index'])
            ->name('equipment.index');
        Route::post('equipment', [EquipmentSettingController::class, 'update'])
            ->name('equipment.update');

    });

/*
|--------------------------------------------------------------------------
| Laravel dashboard redirect
|--------------------------------------------------------------------------
|
| /dashboard -> /admin
|
*/
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

require __DIR__ . '/auth.php';



