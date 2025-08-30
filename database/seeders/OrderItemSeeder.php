<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first order ID from the orders table
        $firstOrderId = DB::table('orders')->value('id');
        
        if (!$firstOrderId) {
            $this->command->warn('No orders found in the database. Skipping OrderItemSeeder.');
            return;
        }

        // Get the first 5 product IDs from the products table
        $productIds = DB::table('products')->pluck('id')->take(5)->toArray();
        
        if (empty($productIds)) {
            $this->command->warn('No products found in the database. Skipping OrderItemSeeder.');
            return;
        }

        DB::table('order_items')->insert([
            [
                'order_id' => $firstOrderId,
                'product_id' => $productIds[0],
                'quantity' => 1,
                'price' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => $firstOrderId,
                'product_id' => $productIds[1]??$productIds[0],
                'quantity' => 2,
                'price' => 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => $firstOrderId,
                'product_id' => $productIds[2]??$productIds[0],
                'quantity' => 3,
                'price' => 30000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => $firstOrderId,
                'product_id' => $productIds[3] ?? $productIds[0],
                'quantity' => 4,
                'price' => 40000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => $firstOrderId,
                'product_id' => $productIds[4] ?? $productIds[0],
                'quantity' => 5,
                'price' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 