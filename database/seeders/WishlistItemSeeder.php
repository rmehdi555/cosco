<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistItemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('wishlist_items')->insert([
            [
                'wishlist_id' => 1,
                'product_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'wishlist_id' => 1,
                'product_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'wishlist_id' => 1,
                'product_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'wishlist_id' => 1,
                'product_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'wishlist_id' => 1,
                'product_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 