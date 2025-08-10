<?php

namespace Database\Seeders;

use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = ProductImage::find(6);
        DB::table('sliders')->insert([
            [
                'title' => 'جدیدترین ها',
                'link' => 'https://rdst.ca/categorys/all',
                'image_url' => asset('storage/' . $product->image_url),
                'type' => 'first_picture',
                'is_show' => 1,
                'target' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'کاسکو',
                'link' => 'https://www.costco.com/join-costco.html?redirectLogin=false',
                'image_url' => asset('storage/' . $product->image_url),
                'type' => 'first_slider',
                'is_show' => 1,
                'target' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'اسلایدر اول',
                'link' => 'https://www.costco.com/join-costco.html?redirectLogin=false',
                'image_url' => asset('storage/' . $product->image_url),
                'type' => 'dsth-aol',
                'is_show' => 1,
                'target' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
