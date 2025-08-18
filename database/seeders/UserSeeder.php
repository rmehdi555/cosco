<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // default admin users
        User::updateOrCreate([
            'cell_phone' => '09100000000',
        ], [
            'phone' => '09100000000',
            'email' => 'info@cosco.com',
            'first_name' => 'سوپرادمین',
            'last_name' => 'ادمین',
            'password' => 'aA123456',
            'type' => UserType::SUPER_ADMIN,
            'is_active' => true,
            'email_verified_at' => now(),
            'verification_code' => null,
            'email_verification_expires_at' => null,
        ]);


    }
}
