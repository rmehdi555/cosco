<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('carts')->insert([
            [
                'user_id' => 1,
                'status' => 'pending',
                'total_amount' => 100000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'status' => 'paid',
                'total_amount' => 200000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'status' => 'shipped',
                'total_amount' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'status' => 'delivered',
                'total_amount' => 300000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'status' => 'cancelled',
                'total_amount' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 