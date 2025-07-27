<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('payments')->insert([
            [
                'order_id' => 1,
                'method' => 'online',
                'status' => 'pending',
                'paid_at' => null,
                'amount' => 100000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 2,
                'method' => 'cash',
                'status' => 'completed',
                'paid_at' => now(),
                'amount' => 200000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 3,
                'method' => 'online',
                'status' => 'completed',
                'paid_at' => now(),
                'amount' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 4,
                'method' => 'online',
                'status' => 'failed',
                'paid_at' => null,
                'amount' => 300000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 5,
                'method' => 'cash',
                'status' => 'completed',
                'paid_at' => now(),
                'amount' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 