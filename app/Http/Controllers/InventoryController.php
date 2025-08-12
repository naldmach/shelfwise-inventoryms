<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'warehouse'])->get();
        $warehouses = Warehouse::all();

        return view('inventory.index', compact('products', 'warehouses'));
    }

    public function stockIn(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'reference_number' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::findOrFail($request->product_id);
            $quantityBefore = $product->quantity;
            $quantityAfter = $quantityBefore + $request->quantity;

            // Update product quantity
            $product->update(['quantity' => $quantityAfter]);

            // Create transaction record
            InventoryTransaction::create([
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'quantity' => $request->quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'notes' => $request->notes,
                'reference_number' => $request->reference_number,
                'transaction_date' => now(),
            ]);
        });

        return redirect()->route('inventory.index')
            ->with('success', 'Stock added successfully.');
    }

    public function stockOut(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'reference_number' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::findOrFail($request->product_id);
            $quantityBefore = $product->quantity;

            if ($quantityBefore < $request->quantity) {
                throw new \Exception('Insufficient stock available.');
            }

            $quantityAfter = $quantityBefore - $request->quantity;

            // Update product quantity
            $product->update(['quantity' => $quantityAfter]);

            // Create transaction record
            InventoryTransaction::create([
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'quantity' => $request->quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'notes' => $request->notes,
                'reference_number' => $request->reference_number,
                'transaction_date' => now(),
            ]);
        });

        return redirect()->route('inventory.index')
            ->with('success', 'Stock removed successfully.');
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'new_quantity' => 'required|integer|min:0',
            'notes' => 'required|string'
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::findOrFail($request->product_id);
            $quantityBefore = $product->quantity;
            $quantityAfter = $request->new_quantity;
            $adjustment = $quantityAfter - $quantityBefore;

            // Update product quantity
            $product->update(['quantity' => $quantityAfter]);

            // Create transaction record
            InventoryTransaction::create([
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'user_id' => Auth::id(),
                'type' => 'adjustment',
                'quantity' => abs($adjustment),
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'notes' => $request->notes,
                'reference_number' => 'ADJ-' . time(),
                'transaction_date' => now(),
            ]);
        });

        return redirect()->route('inventory.index')
            ->with('success', 'Stock adjusted successfully.');
    }

    public function transactions()
    {
        $transactions = InventoryTransaction::with(['product', 'warehouse', 'user'])
            ->latest()
            ->paginate(20);

        return view('inventory.transactions', compact('transactions'));
    }
}
