<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PalletController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExportController;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    // Orders - Available to all authenticated users, but with restrictions in controller
    Route::post('orders/save-view', [OrderController::class, 'saveView'])->name('orders.save-view');
    Route::get('orders/load-view/{view}', [OrderController::class, 'loadView'])->name('orders.load-view');
    Route::delete('orders/delete-view/{view}', [OrderController::class, 'deleteView'])->name('orders.delete-view');
    Route::post('orders/{order}/update-shipping', [OrderController::class, 'updateShipping'])->name('orders.update-shipping');
    Route::post('orders/{order}/upload-photos', [OrderController::class, 'uploadPhotos'])->name('orders.upload-photos');
    Route::delete('order-photos/{photo}', [OrderController::class, 'deletePhoto'])->name('order-photos.delete');
    Route::post('orders/{order}/toggle-verification', [OrderController::class, 'toggleVerification'])->name('orders.toggle-verification');
    Route::resource('orders', OrderController::class);
    
    // Pallets - Staff need 'pallets' permission, School users cannot access
    Route::middleware(['permission:pallets'])->group(function () {
        Route::resource('pallets', PalletController::class);
        Route::get('pallets/{pallet}/orders', [PalletController::class, 'orders'])->name('pallets.orders');
        Route::post('pallets/import', [PalletController::class, 'import'])->name('pallets.import');
        Route::post('pallets/{pallet}/upload-photo', [PalletController::class, 'uploadPhoto'])->name('pallets.upload-photo');
        Route::delete('pallet-photos/{photo}', [PalletController::class, 'deletePhoto'])->name('pallet-photos.delete');
        Route::post('pallets/{pallet}/verify-order/{order}', [PalletController::class, 'verifyOrder'])->name('pallets.verify-order');
        Route::post('pallets/{pallet}/unverify-order/{order}', [PalletController::class, 'unverifyOrder'])->name('pallets.unverify-order');
    });
    
    // Schools - Staff need 'schools' permission
    Route::middleware(['permission:schools'])->group(function () {
        Route::resource('schools', SchoolController::class);
    });
    
    // Users - Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
    
    // Exports - Staff need 'exports' permission
    Route::middleware(['permission:exports'])->group(function () {
        Route::get('exports', [ExportController::class, 'index'])->name('exports.index');
        Route::post('exports/orders', [ExportController::class, 'exportOrders'])->name('exports.orders');
        Route::post('exports/pallets', [ExportController::class, 'exportPallets'])->name('exports.pallets');
    });
});
