<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('addresses')->insert([
            [
                'user_id' => 1,
                'country_id' => 1,
                'province_id' => 1,
                'city_id' => 1,
                'postal_code' => '1111111111',
                'plaque' => '10',
                'address' => 'آدرس نمونه ۱',
                'phone' => '09120000001',
                'is_default' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'country_id' => 1,
                'province_id' => 1,
                'city_id' => 1,
                'postal_code' => '2222222222',
                'plaque' => '20',
                'address' => 'آدرس نمونه ۲',
                'phone' => '09120000002',
                'is_default' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'country_id' => 1,
                'province_id' => 1,
                'city_id' => 1,
                'postal_code' => '3333333333',
                'plaque' => '30',
                'address' => 'آدرس نمونه ۳',
                'phone' => '09120000003',
                'is_default' => false,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 