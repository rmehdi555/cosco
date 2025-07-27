<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('panel:update-permissions');
        Role::createOrFirst(['name' => 'super-admin']);
        Role::createOrFirst(['name' => 'منابع انسانی']);


        Role::where('name', 'منابع انسانی')->first()->syncPermissions([
            'users::view',
        ]);
    }
}
