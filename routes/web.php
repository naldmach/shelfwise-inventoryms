<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Warehouses
    Route::resource('warehouses', WarehouseController::class);

    // Products
    Route::resource('products', ProductController::class);

    // Inventory Management
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
    Route::post('/inventory/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');
    Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::get('/inventory/transactions', [InventoryController::class, 'transactions'])->name('inventory.transactions');

    // Alerts
    Route::resource('alerts', AlertController::class);
    Route::post('/alerts/{alert}/mark-read', [AlertController::class, 'markAsRead'])->name('alerts.mark-read');

    // Reports
    Route::resource('reports', ReportController::class);
    Route::get('/reports/generate/{type}', [ReportController::class, 'generate'])->name('reports.generate');

    // Home route (redirect to dashboard)
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');
});
