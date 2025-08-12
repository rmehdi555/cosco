<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CitySeeder::class,
            CountrySeeder::class,
            ProvinceSeeder::class,
            UserSeeder::class,
            RoleSeeder::class,
            CategoryArticleSeeder::class,
            ArticleSeeder::class,
            AddressSeeder::class,
            ProductCategorySeeder::class,
            WishlistSeeder::class,
            OrderSeeder::class,
            CouponSeeder::class,
            CartSeeder::class,
            PaymentSeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            CartItemSeeder::class,
            ProductReviewSeeder::class,
            WishlistItemSeeder::class,
            ProductImageSeeder::class,
            OrderItemSeeder::class,
            CouponUsageSeeder::class,
            MembershipTypeSeeder::class,
            MembershipSeeder::class,
            RefahSeeder::class,
        ]);
    }
}
