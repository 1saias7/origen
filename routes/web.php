<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CashSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Punto de Venta (POS)
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/search-barcode', [POSController::class, 'searchByBarcode'])->name('pos.search-barcode');
    Route::post('/pos/complete-sale', [POSController::class, 'completeSale'])->name('pos.complete-sale');

    // Inventario
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/all', [InventoryController::class, 'all'])->name('inventory.all');

    // Sesiones de Caja
    Route::get('/cash-session/create', [CashSessionController::class, 'create'])->name('cash-session.create');
    Route::post('/cash-session', [CashSessionController::class, 'store'])->name('cash-session.store');
    Route::get('/cash-session/{cashSession}/close', [CashSessionController::class, 'close'])->name('cash-session.close');
    Route::post('/cash-session/{cashSession}/close', [CashSessionController::class, 'storeClose'])->name('cash-session.store-close');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';