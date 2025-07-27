<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('orders')->insert([
            [
                'user_id' => 1,
                'status' => 'pending',
                'total_amount' => 100000,
                'payment_status' => 'unpaid',
                'shipping_address_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'status' => 'paid',
                'total_amount' => 200000,
                'payment_status' => 'paid',
                'shipping_address_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'status' => 'shipped',
                'total_amount' => 150000,
                'payment_status' => 'paid',
                'shipping_address_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'status' => 'delivered',
                'total_amount' => 300000,
                'payment_status' => 'paid',
                'shipping_address_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'status' => 'cancelled',
                'total_amount' => 50000,
                'payment_status' => 'refunded',
                'shipping_address_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 