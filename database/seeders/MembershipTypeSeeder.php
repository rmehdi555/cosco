<?php

namespace Database\Seeders;

use App\Models\MembershipType;
use Illuminate\Database\Seeder;

class MembershipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $membershipTypes = [
            [
                'name' => 'Basic Membership',
                'description' => 'Basic membership with standard benefits and access to essential features.',
                'price' => 5000000,
                'is_active' => true,
                'day_cycle' => 30,
            ],
            [
                'name' => 'Premium Membership',
                'description' => 'Premium membership with enhanced benefits, priority support, and exclusive content access.',
                'price' => 15000000,
                'is_active' => true,
                'day_cycle' => 90,
            ],
            [
                'name' => 'Gold Membership',
                'description' => 'Gold membership with premium features, special discounts, and VIP customer service.',
                'price' => 30000000,
                'is_active' => true,
                'day_cycle' => 180,
            ],
            [
                'name' => 'Platinum Membership',
                'description' => 'Platinum membership with exclusive benefits, personalized service, and maximum privileges.',
                'price' => 50000000,
                'is_active' => true,
                'day_cycle' => 365,
            ],
            [
                'name' => 'VIP Membership',
                'description' => 'VIP membership with ultimate benefits, 24/7 support, and exclusive access to premium services.',
                'price' => 100000000,
                'is_active' => true,
                'day_cycle' => 730,
            ],
        ];

        foreach ($membershipTypes as $membershipType) {
            MembershipType::updateOrCreate(
                ['name' => $membershipType['name']],
                $membershipType
            );
        }
    }
} 