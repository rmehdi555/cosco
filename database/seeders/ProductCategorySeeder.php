<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_categories')->insert([
            [
                'parent_id' => null,
                'name' => 'دسته اول',
                'slug' => Str::slug('دسته اول'),
                'image_url' => 'category1.jpg',
                'description' => 'توضیحات دسته اول',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => null,
                'name' => 'دسته دوم',
                'slug' => Str::slug('دسته دوم'),
                'image_url' => 'category2.jpg',
                'description' => 'توضیحات دسته دوم',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 1,
                'name' => 'دسته سوم',
                'slug' => Str::slug('دسته سوم'),
                'image_url' => 'category3.jpg',
                'description' => 'توضیحات دسته سوم',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_id' => 1,
                'name' => 'دسته چهارم',
                'slug' => Str::slug('دسته چهارم'),
                'image_url' => 'category4.jpg',
                'description' => 'توضیحات دسته چهارم',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],            [
                'parent_id' => 4,
                'name' => 'دسته پنجم',
                'slug' => Str::slug('دسته پنجم'),
                'image_url' => 'category5.jpg',
                'description' => 'توضیحات دسته پنجم',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 