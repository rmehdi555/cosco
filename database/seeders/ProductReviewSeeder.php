<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_reviews')->insert([
            [
                'user_id' => 1,
                'product_id' => 1,
                'rating' => 5,
                'comment' => 'نظر نمونه ۱',
                'approved' => true,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'product_id' => 2,
                'rating' => 4,
                'comment' => 'نظر نمونه ۲',
                'approved' => true,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'product_id' => 3,
                'rating' => 3,
                'comment' => 'نظر نمونه ۳',
                'approved' => false,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'product_id' => 4,
                'rating' => 2,
                'comment' => 'نظر نمونه ۴',
                'approved' => false,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'product_id' => 5,
                'rating' => 1,
                'comment' => 'نظر نمونه ۵',
                'approved' => true,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 