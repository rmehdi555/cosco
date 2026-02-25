<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'product_category_id' => 1,
                'brand_id' => 1,
                'name' => 'محصول اول',
                'code' => 'PRD-001',
                'slug' => Str::slug('محصول اول'),
                'description' => 'توضیحات محصول اول',
                'price' => 10000,
                'stock' => 10,
                'is_active' => true,
                'is_featured' => false,
                'is_online_only' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_category_id' => 1,
                'brand_id' => 2,
                'name' => 'محصول دوم',
                'code' => 'PRD-002',
                'slug' => Str::slug('محصول دوم'),
                'description' => 'توضیحات محصول دوم',
                'price' => 20000,
                'stock' => 20,
                'is_active' => true,
                'is_featured' => true,
                'is_online_only' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_category_id' => 1,
                'brand_id' => 3,
                'name' => 'محصول سوم',
                'code' => 'PRD-003',
                'slug' => Str::slug('محصول سوم'),
                'description' => 'توضیحات محصول سوم',
                'price' => 30000,
                'stock' => 30,
                'is_active' => false,
                'is_featured' => false,
                'is_online_only' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_category_id' => 4,
                'brand_id' => 4,
                'name' => 'محصول چهارم',
                'code' => 'PRD-004',
                'slug' => Str::slug('محصول چهارم'),
                'description' => 'توضیحات محصول چهارم',
                'price' => 40000,
                'stock' => 40,
                'is_active' => true,
                'is_featured' => false,
                'is_online_only' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_category_id' => 5,
                'brand_id' => 5,
                'name' => 'محصول پنجم',
                'code' => 'PRD-005',
                'slug' => Str::slug('محصول پنجم'),
                'description' => 'توضیحات محصول پنجم',
                'price' => 50000,
                'stock' => 50,
                'is_active' => true,
                'is_featured' => true,
                'is_online_only' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 