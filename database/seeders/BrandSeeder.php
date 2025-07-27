<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('brands')->insert([
            [
                'name' => 'برند اول',
                'slug' => Str::slug('برند اول'),
                'image_url' => 'brand1.jpg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'برند دوم',
                'slug' => Str::slug('برند دوم'),
                'image_url' => 'brand2.jpg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'برند سوم',
                'slug' => Str::slug('برند سوم'),
                'image_url' => 'brand3.jpg',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'برند چهارم',
                'slug' => Str::slug('برند چهارم'),
                'image_url' => 'brand4.jpg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'برند پنجم',
                'slug' => Str::slug('برند پنجم'),
                'image_url' => 'brand5.jpg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 