<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponUsageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('coupon_usages')->insert([
            [
                'coupon_id' => 1,
                'order_id' => 1,
                'used_at' => now()->subDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coupon_id' => 2,
                'order_id' => 2,
                'used_at' => now()->subDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coupon_id' => 3,
                'order_id' => 3,
                'used_at' => now()->subDays(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 