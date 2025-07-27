<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('article_categories')->insert([
            [
                'name' => 'اخبار سایت',
                'slug' => Str::slug('اخبار سایت'),
                'is_show' => 1,
                'description' => 'دسته اخبار سایت',
                'image_url' => null,
                'seo_title' => 'اخبار سایت',
                'seo_description' => 'توضیحات سئو اخبار سایت',
                'seo_follow' => 1,
                'seo_index' => 1,
                'seo_canonical' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'مقالات آموزشی',
                'slug' => Str::slug('مقالات آموزشی'),
                'is_show' => 1,
                'description' => 'دسته مقالات آموزشی',
                'image_url' => null,
                'seo_title' => 'مقالات آموزشی',
                'seo_description' => 'توضیحات سئو مقالات آموزشی',
                'seo_follow' => 1,
                'seo_index' => 1,
                'seo_canonical' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 