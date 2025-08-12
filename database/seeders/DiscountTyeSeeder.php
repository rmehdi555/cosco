<?php

namespace Database\Seeders;

use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountTyeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = ProductImage::find(5);
        DB::table('discount_types')->insert([
            [
                'title' => 'لوازم خانگی تخفیف دار ماه مرداد1404/05/20',
                'background_color_up' => '#FF0000',
                'background_color_down' => '#00FF00',
                'ads_image_url' => 'http://localhost:8072/storage/product-images/01K1ZVJ3P079NZDQH8TP4N5692.jpg',
                'image_url' => asset('storage/' . $product->image_url),
                'footer' => 'خرید آنلاین | معتبر تا 1404/6/10 | آخرین تخفیف سال',
                'is_show' => 1,
                'ads_title' => 'بیشترین تخفیف ها | تعداد محدود | اتمام تا تاریخ 1404/05/20',
                'ads_footer' => 'معتبر تا 1404/06/06',
                'link' => 'https://www.costco.com/appliances.html?keyword=Price',
                'ads_link' => 'https://www.costco.com/s?dept=All&keyword=costco%20direct',
                'target' => 1,
                'ads_target' => 1,
                'product_category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
