<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Main Warehouse',
                'description' => 'Primary storage facility for all products',
                'address' => '123 Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'country' => 'USA',
                'phone' => '+1-555-0123',
                'email' => 'main@warehouse.com'
            ],
            [
                'name' => 'West Coast Distribution',
                'description' => 'West coast distribution center',
                'address' => '456 West Avenue',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip_code' => '90210',
                'country' => 'USA',
                'phone' => '+1-555-0456',
                'email' => 'west@warehouse.com'
            ],
            [
                'name' => 'Regional Storage',
                'description' => 'Regional storage facility for Midwest',
                'address' => '789 Regional Drive',
                'city' => 'Chicago',
                'state' => 'IL',
                'zip_code' => '60601',
                'country' => 'USA',
                'phone' => '+1-555-0789',
                'email' => 'regional@warehouse.com'
            ]
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }
    }
}
