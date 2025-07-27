<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Iran
        DB::table('countries')->insert([
            'id' => '1',
            'title_fa' => 'ایران',
            'title_en' => 'Iran'
        ]);
    }
}
