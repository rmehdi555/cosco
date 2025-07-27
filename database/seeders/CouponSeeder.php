<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('coupons')->insert([
            [
                'code' => 'OFF10',
                'discount_percent' => 10,
                'discount_amount' => null,
                'expires_at' => now()->addDays(10),
                'max_uses' => 100,
                'used_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OFF20',
                'discount_percent' => null,
                'discount_amount' => 20000,
                'expires_at' => now()->addDays(20),
                'max_uses' => 50,
                'used_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OFF30',
                'discount_percent' => 30,
                'discount_amount' => null,
                'expires_at' => now()->addDays(30),
                'max_uses' => 10,
                'used_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 