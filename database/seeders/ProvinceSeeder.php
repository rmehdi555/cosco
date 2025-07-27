<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class provinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // East Azarbaijan province
        DB::table('provinces')->insert([
            'id' => '1',
            'title_fa' => 'آذربایجان شرقی',
            'title_en' => 'East Azarbaijan',
            'country_id' => '1'
        ]);

        // West Azarbaijan province
        DB::table('provinces')->insert([
            'id' => '2',
            'title_fa' => 'آذربایجان غربی',
            'title_en' => 'West Azarbaijan',
            'country_id' => '1'
        ]);

        // Ardabil province
        DB::table('provinces')->insert([
            'id' => '3',
            'title_fa' => 'اردبیل',
            'title_en' => 'Ardabil',
            'country_id' => '1'
        ]);

        // Isfahan province
        DB::table('provinces')->insert([
            'id' => '4',
            'title_fa' => 'اصفهان',
            'title_en' => 'Isfahan',
            'country_id' => '1'
        ]);

        // Alborz province
        DB::table('provinces')->insert([
            'id' => '5',
            'title_fa' => 'البرز',
            'title_en' => 'Alborz',
            'country_id' => '1'
        ]);

        // Ilam province
        DB::table('provinces')->insert([
            'id' => '6',
            'title_fa' => 'ایلام',
            'title_en' => 'Ilam',
            'country_id' => '1'
        ]);

        // Bushehr province
        DB::table('provinces')->insert([
            'id' => '7',
            'title_fa' => 'بوشهر',
            'title_en' => 'Bushehr',
            'country_id' => '1'
        ]);

        // Tehran province
        DB::table('provinces')->insert([
            'id' => '8',
            'title_fa' => 'تهران',
            'title_en' => 'Tehran',
            'country_id' => '1'
        ]);

        // Chaharmahal and Bakhtiari province
        DB::table('provinces')->insert([
            'id' => '9',
            'title_fa' => 'چهارمحال و بختیاری',
            'title_en' => 'Chaharmahal and Bakhtiari',
            'country_id' => '1'
        ]);

        // South Khorasan province
        DB::table('provinces')->insert([
            'id' => '10',
            'title_fa' => 'خراسان جنوبی',
            'title_en' => 'South Khorasan',
            'country_id' => '1'
        ]);

        // Razavi Khorasan province
        DB::table('provinces')->insert([
            'id' => '11',
            'title_fa' => 'خراسان رضوی',
            'title_en' => 'Razavi Khorasan',
            'country_id' => '1'
        ]);

        // North Khorasan province
        DB::table('provinces')->insert([
            'id' => '12',
            'title_fa' => 'خراسان شمالی',
            'title_en' => 'North Khorasan',
            'country_id' => '1'
        ]);

        // Khuzestan province
        DB::table('provinces')->insert([
            'id' => '13',
            'title_fa' => 'خوزستان',
            'title_en' => 'Khuzestan',
            'country_id' => '1'
        ]);

        // Zanjan province
        DB::table('provinces')->insert([
            'id' => '14',
            'title_fa' => 'زنجان',
            'title_en' => 'Zanjan',
            'country_id' => '1'
        ]);

        // Semnan province
        DB::table('provinces')->insert([
            'id' => '15',
            'title_fa' => 'سمنان',
            'title_en' => 'Semnan',
            'country_id' => '1'
        ]);

        // Sistan and Baluchestan province
        DB::table('provinces')->insert([
            'id' => '16',
            'title_fa' => 'سیستان و بلوچستان',
            'title_en' => 'Sistan and Baluchestan',
            'country_id' => '1'
        ]);

        // Fars province
        DB::table('provinces')->insert([
            'id' => '17',
            'title_fa' => 'فارس',
            'title_en' => 'Fars',
            'country_id' => '1'
        ]);

        // Qazvin province
        DB::table('provinces')->insert([
            'id' => '18',
            'title_fa' => 'قزوین',
            'title_en' => 'Qazvin',
            'country_id' => '1'
        ]);

        // Qom province
        DB::table('provinces')->insert([
            'id' => '19',
            'title_fa' => 'قم',
            'title_en' => 'Qom',
            'country_id' => '1'
        ]);

        // Kurdistan province
        DB::table('provinces')->insert([
            'id' => '20',
            'title_fa' => 'کردستان',
            'title_en' => 'Kurdistan',
            'country_id' => '1'
        ]);

        // Kerman province
        DB::table('provinces')->insert([
            'id' => '21',
            'title_fa' => 'کرمان',
            'title_en' => 'Kerman',
            'country_id' => '1'
        ]);

        // Kermanshah province
        DB::table('provinces')->insert([
            'id' => '22',
            'title_fa' => 'کرمانشاه',
            'title_en' => 'Kermanshah',
            'country_id' => '1'
        ]);

        // Kohgiluyeh and Boyer-Ahmad province
        DB::table('provinces')->insert([
            'id' => '23',
            'title_fa' => 'کهگیلویه و بویراحمد',
            'title_en' => 'Kohgiluyeh and Boyer-Ahmad',
            'country_id' => '1'
        ]);

        // Golestan province
        DB::table('provinces')->insert([
            'id' => '24',
            'title_fa' => 'گلستان',
            'title_en' => 'Golestan',
            'country_id' => '1'
        ]);

        // Gilan province
        DB::table('provinces')->insert([
            'id' => '25',
            'title_fa' => 'گیلان	',
            'title_en' => 'Gilan',
            'country_id' => '1'
        ]);

        // Lorestan province
        DB::table('provinces')->insert([
            'id' => '26',
            'title_fa' => 'لرستان',
            'title_en' => 'Lorestan',
            'country_id' => '1'
        ]);

        // Mazandaran province
        DB::table('provinces')->insert([
            'id' => '27',
            'title_fa' => 'مازندران',
            'title_en' => 'Mazandaran',
            'country_id' => '1'
        ]);

        // Markazi province
        DB::table('provinces')->insert([
            'id' => '28',
            'title_fa' => 'مرکزی',
            'title_en' => 'Markazi',
            'country_id' => '1'
        ]);

        // Hormozgan province
        DB::table('provinces')->insert([
            'id' => '29',
            'title_fa' => 'هرمزگان',
            'title_en' => 'Hormozgan',
            'country_id' => '1'
        ]);

        // Hamadan province
        DB::table('provinces')->insert([
            'id' => '30',
            'title_fa' => 'همدان',
            'title_en' => 'Hamadan',
            'country_id' => '1'
        ]);

        // Yazd province
        DB::table('provinces')->insert([
            'id' => '31',
            'title_fa' => 'یزد',
            'title_en' => 'Yazd',
            'country_id' => '1'
        ]);
    }
}
