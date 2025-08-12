<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\Alert;
use App\Models\InventoryTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_warehouses' => Warehouse::count(),
            'low_stock_products' => Product::lowStock()->count(),
            'out_of_stock_products' => Product::outOfStock()->count(),
            'unread_alerts' => Alert::unread()->count(),
            'recent_transactions' => InventoryTransaction::with(['product', 'warehouse', 'user'])
                ->latest()
                ->take(10)
                ->get(),
            'recent_alerts' => Alert::with(['product', 'warehouse'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('dashboard', $data);
    }
}
