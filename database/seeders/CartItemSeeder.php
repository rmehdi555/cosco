<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartItemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cart_items')->insert([
            [
                'cart_id' => 1,
                'product_id' => 1,
                'quantity' => 1,
                'price' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cart_id' => 1,
                'product_id' => 2,
                'quantity' => 2,
                'price' => 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cart_id' => 1,
                'product_id' => 3,
                'quantity' => 3,
                'price' => 30000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cart_id' => 1,
                'product_id' => 4,
                'quantity' => 4,
                'price' => 40000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cart_id' => 1,
                'product_id' => 5,
                'quantity' => 5,
                'price' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 