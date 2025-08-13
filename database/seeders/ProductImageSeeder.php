<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_images')->insert([
            [
                'product_id' => 1,
                'image_url' => 'product-images/01K29NKBWHNPB5E48HW6V0Y1PV.jpg',
                'is_main' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 2,
                'image_url' => 'product-images/01K29NKBWHNPB5E48HW6V0Y1PV.jpg',
                'is_main' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 3,
                'image_url' => 'product-images/01K29NKBWHNPB5E48HW6V0Y1PV.jpg',
                'is_main' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 4,
                'image_url' => 'product-images/01K29NKBWHNPB5E48HW6V0Y1PV.jpg',
                'is_main' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 5,
                'image_url' => 'product-images/01K29NKBWHNPB5E48HW6V0Y1PV.jpg',
                'is_main' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
