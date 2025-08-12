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

    // API routes for Vue.js components (using web authentication)
    Route::prefix('api')->group(function () {
        // Dashboard API routes
        Route::get('/dashboard/stats', function () {
            return response()->json([
                'total_products' => \App\Models\Product::count(),
                'total_categories' => \App\Models\Category::count(),
                'total_warehouses' => \App\Models\Warehouse::count(),
                'low_stock_products' => \App\Models\Product::lowStock()->count(),
            ]);
        });

        Route::get('/dashboard/transactions', function () {
            $transactions = \App\Models\InventoryTransaction::with(['product', 'warehouse', 'user'])
                ->latest()
                ->take(10)
                ->get();
            return response()->json($transactions);
        });

        Route::get('/dashboard/alerts', function () {
            $alerts = \App\Models\Alert::with(['product', 'warehouse'])
                ->latest()
                ->take(5)
                ->get();
            return response()->json($alerts);
        });

        Route::get('/dashboard/stock-alerts', function () {
            return response()->json([
                'low_stock' => \App\Models\Product::lowStock()->with(['category', 'warehouse'])->get(),
                'out_of_stock' => \App\Models\Product::outOfStock()->with(['category', 'warehouse'])->get(),
            ]);
        });

        // Products API routes
        Route::get('/products', function () {
            $products = \App\Models\Product::with(['category', 'warehouse'])->get();
            return response()->json($products);
        });

        Route::get('/products/low-stock', function () {
            $products = \App\Models\Product::lowStock()->with(['category', 'warehouse'])->get();
            return response()->json($products);
        });

        // Categories API routes
        Route::get('/categories', function () {
            $categories = \App\Models\Category::withCount('products')->get();
            return response()->json($categories);
        });

        // Warehouses API routes
        Route::get('/warehouses', function () {
            $warehouses = \App\Models\Warehouse::withCount('products')->get();
            return response()->json($warehouses);
        });

        // Inventory API routes
        Route::get('/inventory/transactions', function () {
            $transactions = \App\Models\InventoryTransaction::with(['product', 'warehouse', 'user'])
                ->latest()
                ->take(20)
                ->get();
            return response()->json($transactions);
        });

        // Alerts API routes
        Route::get('/alerts', function () {
            $alerts = \App\Models\Alert::with(['product', 'warehouse'])
                ->latest()
                ->get();
            return response()->json($alerts);
        });

        // Reports API routes
        Route::get('/reports', function () {
            $reports = \App\Models\Report::latest()->get();
            return response()->json($reports);
        });

        Route::get('/reports/{id}', function ($id) {
            $report = \App\Models\Report::findOrFail($id);
            return response()->json($report);
        });
    });
});
