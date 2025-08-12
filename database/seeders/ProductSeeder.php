<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Warehouse;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $warehouses = Warehouse::all();

        $products = [
            [
                'name' => 'Laptop Computer',
                'sku' => 'LAP-001',
                'description' => 'High-performance laptop with latest specifications',
                'price' => 999.99,
                'quantity' => 25,
                'min_quantity' => 5,
                'max_quantity' => 100,
                'unit' => 'pcs',
                'barcode' => '1234567890123',
                'category_id' => $categories->where('name', 'Electronics')->first()->id,
                'warehouse_id' => $warehouses->first()->id
            ],
            [
                'name' => 'Smartphone',
                'sku' => 'PHN-001',
                'description' => 'Latest smartphone with advanced features',
                'price' => 699.99,
                'quantity' => 50,
                'min_quantity' => 10,
                'max_quantity' => 200,
                'unit' => 'pcs',
                'barcode' => '1234567890124',
                'category_id' => $categories->where('name', 'Electronics')->first()->id,
                'warehouse_id' => $warehouses->first()->id
            ],
            [
                'name' => 'T-Shirt',
                'sku' => 'TSH-001',
                'description' => 'Cotton t-shirt in various sizes',
                'price' => 19.99,
                'quantity' => 100,
                'min_quantity' => 20,
                'max_quantity' => 500,
                'unit' => 'pcs',
                'barcode' => '1234567890125',
                'category_id' => $categories->where('name', 'Clothing')->first()->id,
                'warehouse_id' => $warehouses->first()->id
            ],
            [
                'name' => 'Programming Book',
                'sku' => 'BOK-001',
                'description' => 'Learn Laravel framework development',
                'price' => 49.99,
                'quantity' => 30,
                'min_quantity' => 5,
                'max_quantity' => 100,
                'unit' => 'pcs',
                'barcode' => '1234567890126',
                'category_id' => $categories->where('name', 'Books')->first()->id,
                'warehouse_id' => $warehouses->first()->id
            ],
            [
                'name' => 'Garden Tool Set',
                'sku' => 'GRD-001',
                'description' => 'Complete set of essential garden tools',
                'price' => 89.99,
                'quantity' => 15,
                'min_quantity' => 3,
                'max_quantity' => 50,
                'unit' => 'sets',
                'barcode' => '1234567890127',
                'category_id' => $categories->where('name', 'Home & Garden')->first()->id,
                'warehouse_id' => $warehouses->first()->id
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
