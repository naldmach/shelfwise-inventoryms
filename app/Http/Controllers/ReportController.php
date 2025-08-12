<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\InventoryTransaction;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::where('user_id', Auth::id())->latest()->paginate(15);
        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:inventory_summary,low_stock,transaction_history,warehouse_summary',
            'parameters' => 'nullable|array'
        ]);

        $report = Report::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type,
            'parameters' => $request->parameters,
            'status' => 'pending'
        ]);

        return redirect()->route('reports.index')
            ->with('success', 'Report generation started.');
    }

    public function show(string $id)
    {
        $report = Report::findOrFail($id);
        return view('reports.show', compact('report'));
    }

    public function generate($type)
    {
        $data = [];

        switch ($type) {
            case 'inventory_summary':
                $data = [
                    'total_products' => Product::count(),
                    'total_value' => Product::sum(DB::raw('price * quantity')),
                    'low_stock_count' => Product::lowStock()->count(),
                    'out_of_stock_count' => Product::outOfStock()->count(),
                    'products_by_category' => Product::with('category')
                        ->get()
                        ->groupBy('category.name')
                        ->map(function($products) {
                            return $products->count();
                        })
                ];
                break;

            case 'low_stock':
                $data = Product::lowStock()->with(['category', 'warehouse'])->get();
                break;

            case 'transaction_history':
                $data = InventoryTransaction::with(['product', 'warehouse', 'user'])
                    ->latest()
                    ->take(100)
                    ->get();
                break;

            case 'warehouse_summary':
                $data = Warehouse::withCount('products')->get();
                break;
        }

        return view('reports.generated', compact('data', 'type'));
    }

    public function destroy(string $id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Report deleted successfully.');
    }
}
