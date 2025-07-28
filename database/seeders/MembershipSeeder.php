<?php

namespace Database\Seeders;

use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users and membership types
        $users = User::take(5)->get();
        $membershipTypes = MembershipType::all();

        if ($users->count() === 0) {
            // Create a default user if none exists
            $users = collect([User::first()]);
        }

        if ($membershipTypes->count() === 0) {
            // Run MembershipTypeSeeder if no types exist
            $this->call(MembershipTypeSeeder::class);
            $membershipTypes = MembershipType::all();
        }

        $memberships = [
            [
                'serial_number' => 'MEM-001-2024',
                'user_id' => $users->first()->id,
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(335),
                'is_active' => true,
                'membership_type_id' => $membershipTypes->first()->id,
            ],
            [
                'serial_number' => 'MEM-002-2024',
                'user_id' => $users->count() > 1 ? $users[1]->id : $users->first()->id,
                'start_date' => now()->subDays(15),
                'end_date' => now()->addDays(75),
                'is_active' => true,
                'membership_type_id' => $membershipTypes->count() > 1 ? $membershipTypes[1]->id : $membershipTypes->first()->id,
            ],
            [
                'serial_number' => 'MEM-003-2024',
                'user_id' => $users->count() > 2 ? $users[2]->id : $users->first()->id,
                'start_date' => now()->subDays(60),
                'end_date' => now()->addDays(120),
                'is_active' => true,
                'membership_type_id' => $membershipTypes->count() > 2 ? $membershipTypes[2]->id : $membershipTypes->first()->id,
            ],
            [
                'serial_number' => 'MEM-004-2024',
                'user_id' => $users->count() > 3 ? $users[3]->id : $users->first()->id,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(355),
                'is_active' => true,
                'membership_type_id' => $membershipTypes->count() > 3 ? $membershipTypes[3]->id : $membershipTypes->first()->id,
            ],
            [
                'serial_number' => 'MEM-005-2024',
                'user_id' => $users->count() > 4 ? $users[4]->id : $users->first()->id,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(725),
                'is_active' => true,
                'membership_type_id' => $membershipTypes->count() > 4 ? $membershipTypes[4]->id : $membershipTypes->first()->id,
            ],
        ];

        foreach ($memberships as $membership) {
            Membership::updateOrCreate(
                ['serial_number' => $membership['serial_number']],
                $membership
            );
        }
    }
} 