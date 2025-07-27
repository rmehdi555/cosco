<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('articles')->insert([
            [
                'category_id' => 1,
                'user_id' => 1,
                'title' => 'مقاله اول',
                'slug' => Str::slug('مقاله اول'),
                'excerpt' => 'خلاصه مقاله اول',
                'body' => 'متن کامل مقاله اول',
                'is_show' => 1,
                'image_url' => null,
                'view_count' => 0,
                'is_future' => false,
                'seo_title' => 'سئو مقاله اول',
                'seo_description' => 'توضیحات سئو مقاله اول',
                'seo_follow' => 1,
                'seo_index' => 1,
                'seo_canonical' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2,
                'user_id' => 1,
                'title' => 'مقاله دوم',
                'slug' => Str::slug('مقاله دوم'),
                'excerpt' => 'خلاصه مقاله دوم',
                'body' => 'متن کامل مقاله دوم',
                'is_show' => 1,
                'image_url' => null,
                'view_count' => 0,
                'is_future' => false,
                'seo_title' => 'سئو مقاله دوم',
                'seo_description' => 'توضیحات سئو مقاله دوم',
                'seo_follow' => 1,
                'seo_index' => 1,
                'seo_canonical' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1,
                'user_id' => 1,
                'title' => 'مقاله سوم',
                'slug' => Str::slug('مقاله سوم'),
                'excerpt' => 'خلاصه مقاله سوم',
                'body' => 'متن کامل مقاله سوم',
                'is_show' => 1,
                'image_url' => null,
                'view_count' => 0,
                'is_future' => false,
                'seo_title' => 'سئو مقاله سوم',
                'seo_description' => 'توضیحات سئو مقاله سوم',
                'seo_follow' => 1,
                'seo_index' => 1,
                'seo_canonical' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2,
                'user_id' => 1,
                'title' => 'مقاله چهارم',
                'slug' => Str::slug('مقاله چهارم'),
                'excerpt' => 'خلاصه مقاله چهارم',
                'body' => 'متن کامل مقاله چهارم',
                'is_show' => 1,
                'image_url' => null,
                'view_count' => 0,
                'is_future' => false,
                'seo_title' => 'سئو مقاله چهارم',
                'seo_description' => 'توضیحات سئو مقاله چهارم',
                'seo_follow' => 1,
                'seo_index' => 1,
                'seo_canonical' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1,
                'user_id' => 1,
                'title' => 'مقاله پنجم',
                'slug' => Str::slug('مقاله پنجم'),
                'excerpt' => 'خلاصه مقاله پنجم',
                'body' => 'متن کامل مقاله پنجم',
                'is_show' => 1,
                'image_url' => null,
                'view_count' => 0,
                'is_future' => false,
                'seo_title' => 'سئو مقاله پنجم',
                'seo_description' => 'توضیحات سئو مقاله پنجم',
                'seo_follow' => 1,
                'seo_index' => 1,
                'seo_canonical' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 