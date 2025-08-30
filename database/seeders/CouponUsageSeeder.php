<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponUsageSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first 3 coupon IDs from the coupons table
        $couponIds = DB::table('coupons')->pluck('id')->take(3)->toArray();
        
        if (empty($couponIds)) {
            $this->command->warn('No coupons found in the database. Skipping CouponUsageSeeder.');
            return;
        }

        // Get the first 3 order IDs from the orders table
        $orderIds = DB::table('orders')->pluck('id')->take(3)->toArray();
        
        if (empty($orderIds)) {
            $this->command->warn('No orders found in the database. Skipping CouponUsageSeeder.');
            return;
        }

        DB::table('coupon_usages')->insert([
            [
                'coupon_id' => $couponIds[0] ?? $couponIds[0],
                'order_id' => $orderIds[0] ?? $orderIds[0],
                'used_at' => now()->subDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coupon_id' => $couponIds[1] ?? $couponIds[0],
                'order_id' => $orderIds[1] ?? $orderIds[0],
                'used_at' => now()->subDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'coupon_id' => $couponIds[2] ?? $couponIds[0],
                'order_id' => $orderIds[2] ?? $orderIds[0],
                'used_at' => now()->subDays(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 