<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RefahOrganization;
use App\Models\RefahCart;

class RefahSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Create sample organizations
        RefahOrganization::create([
            'title' => 'سازمان رفاه کارکنان',
            'is_active' => true,
        ]);

        RefahOrganization::create([
            'title' => 'صندوق رفاه اجتماعی',
            'is_active' => true,
        ]);

        // Create sample refah carts
        RefahCart::create([
            'title' => 'بسته رفاهی پایه',
            'description' => 'شامل اقلام ضروری و مواد غذایی اساسی',
            'price' => 500000,
            'is_active' => true,
        ]);

        RefahCart::create([
            'title' => 'بسته رفاهی متوسط',
            'description' => 'شامل اقلام غذایی، بهداشتی و تفریحی',
            'price' => 1000000,
            'is_active' => true,
        ]);

        RefahCart::create([
            'title' => 'بسته رفاهی کامل',
            'description' => 'شامل تمامی اقلام ضروری، غذایی، بهداشتی و هدایای ویژه',
            'price' => 1500000,
            'is_active' => true,
        ]);

        RefahCart::create([
            'title' => 'بسته رفاهی ویژه',
            'description' => 'بسته لوکس شامل محصولات درجه یک و هدایای ارزشمند',
            'price' => 2000000,
            'is_active' => true,
        ]);
    }
}
