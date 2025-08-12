<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and components', 'color' => '#3B82F6'],
            ['name' => 'Clothing', 'description' => 'Apparel and accessories', 'color' => '#10B981'],
            ['name' => 'Books', 'description' => 'Books and publications', 'color' => '#F59E0B'],
            ['name' => 'Home & Garden', 'description' => 'Home improvement and garden supplies', 'color' => '#8B5CF6'],
            ['name' => 'Sports', 'description' => 'Sports equipment and gear', 'color' => '#EF4444'],
            ['name' => 'Automotive', 'description' => 'Automotive parts and accessories', 'color' => '#6B7280'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
