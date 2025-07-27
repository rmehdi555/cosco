<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // East Azarbaijan / Tabriz
        DB::table('cities')->insert([
            'id' => '1',
            'title_fa' => 'تبریز',
            'title_en' => 'Tabriz',
            'province_id' => '1'
        ]);

        // West Azarbaijan / Urmia
        DB::table('cities')->insert([
            'id' => '2',
            'title_fa' => 'ارومیه',
            'title_en' => 'Urmia',
            'province_id' => '2'
        ]);

        // Ardabil / Ardabil
        DB::table('cities')->insert([
            'id' => '3',
            'title_fa' => 'اردبیل',
            'title_en' => 'Ardabil',
            'province_id' => '3'
        ]);

        // Isfahan / Isfahan
        DB::table('cities')->insert([
            'id' => '4',
            'title_fa' => 'اصفهان',
            'title_en' => 'Isfahan',
            'province_id' => '4'
        ]);

        // Alborz / Karaj
        DB::table('cities')->insert([
            'id' => '5',
            'title_fa' => 'کرج',
            'title_en' => 'Karaj',
            'province_id' => '5'
        ]);

        // Ilam / Ilam
        DB::table('cities')->insert([
            'id' => '6',
            'title_fa' => 'ایلام',
            'title_en' => 'Ilam',
            'province_id' => '6'
        ]);

        // Bushehr / Bushehr
        DB::table('cities')->insert([
            'id' => '7',
            'title_fa' => 'بوشهر',
            'title_en' => 'Bushehr',
            'province_id' => '7'
        ]);

        // Tehran province
        DB::table('cities')->insert([
            'id' => '8',
            'title_fa' => 'تهران',
            'title_en' => 'Tehran',
            'province_id' => '8'
        ]);

        // Chaharmahal and Bakhtiari / Shahr-e Kord
        DB::table('cities')->insert([
            'id' => '9',
            'title_fa' => 'شهر کرد',
            'title_en' => 'Shahr-e Kord',
            'province_id' => '9'
        ]);

        // South Khorasan / Birjand
        DB::table('cities')->insert([
            'id' => '10',
            'title_fa' => 'بیرجند',
            'title_en' => 'Birjand',
            'province_id' => '10'
        ]);

        // Razavi Khorasan / Mashhad
        DB::table('cities')->insert([
            'id' => '11',
            'title_fa' => 'مشهد',
            'title_en' => 'Mashhad',
            'province_id' => '11'
        ]);

        // North Khorasan / Bojnord
        DB::table('cities')->insert([
            'id' => '12',
            'title_fa' => 'بجنورد',
            'title_en' => 'Bojnord',
            'province_id' => '12'
        ]);

        // Khuzestan / Ahvaz
        DB::table('cities')->insert([
            'id' => '13',
            'title_fa' => 'اهواز',
            'title_en' => 'Ahvaz',
            'province_id' => '13'
        ]);

        // Zanjan / Zanjan
        DB::table('cities')->insert([
            'id' => '14',
            'title_fa' => 'زنجان',
            'title_en' => 'Zanjan',
            'province_id' => '14'
        ]);

        // Semnan / Semnan
        DB::table('cities')->insert([
            'id' => '15',
            'title_fa' => 'سمنان',
            'title_en' => 'Semnan',
            'province_id' => '15'
        ]);

        // Sistan and Baluchestan / Zahedan
        DB::table('cities')->insert([
            'id' => '16',
            'title_fa' => 'زاهدان',
            'title_en' => 'Zahedan',
            'province_id' => '16'
        ]);

        // Fars / Shiraz
        DB::table('cities')->insert([
            'id' => '17',
            'title_fa' => 'شیراز',
            'title_en' => 'Shiraz',
            'province_id' => '17'
        ]);

        // Qazvin / Qazvin
        DB::table('cities')->insert([
            'id' => '18',
            'title_fa' => 'قزوین',
            'title_en' => 'Qazvin',
            'province_id' => '18'
        ]);

        // Qom / Qom
        DB::table('cities')->insert([
            'id' => '19',
            'title_fa' => 'قم',
            'title_en' => 'Qom',
            'province_id' => '19'
        ]);

        // Kurdistan / Sanandaj
        DB::table('cities')->insert([
            'id' => '20',
            'title_fa' => 'سنندج',
            'title_en' => 'Sanandaj',
            'province_id' => '20'
        ]);

        // Kerman / Kerman
        DB::table('cities')->insert([
            'id' => '21',
            'title_fa' => 'کرمان',
            'title_en' => 'Kerman',
            'province_id' => '21'
        ]);

        // Kermanshah / Kermanshah
        DB::table('cities')->insert([
            'id' => '22',
            'title_fa' => 'کرمانشاه',
            'title_en' => 'Kermanshah',
            'province_id' => '22'
        ]);

        // Kohgiluyeh and Boyer-Ahmad / Yasuj
        DB::table('cities')->insert([
            'id' => '23',
            'title_fa' => 'یاسوج',
            'title_en' => 'Yasuj',
            'province_id' => '23'
        ]);

        // Golestan / Gorgan
        DB::table('cities')->insert([
            'id' => '24',
            'title_fa' => 'گرگان',
            'title_en' => 'Gorgan',
            'province_id' => '24'
        ]);

        // Gilan / Rasht
        DB::table('cities')->insert([
            'id' => '25',
            'title_fa' => 'رشت	',
            'title_en' => 'Rasht',
            'province_id' => '25'
        ]);

        // Lorestan / Khorramabad
        DB::table('cities')->insert([
            'id' => '26',
            'title_fa' => 'خرم آباد',
            'title_en' => 'Khorramabad',
            'province_id' => '26'
        ]);

        // Mazandaran / Sari
        DB::table('cities')->insert([
            'id' => '27',
            'title_fa' => 'ساری',
            'title_en' => 'Sari',
            'province_id' => '27'
        ]);

        // Markazi / Arak
        DB::table('cities')->insert([
            'id' => '28',
            'title_fa' => 'اراک',
            'title_en' => 'Arak',
            'province_id' => '28'
        ]);

        // Hormozgan / Bandar Abbas
        DB::table('cities')->insert([
            'id' => '29',
            'title_fa' => 'بندر عباس',
            'title_en' => 'Bandar Abbas',
            'province_id' => '29'
        ]);

        // Hamadan / Hamadan
        DB::table('cities')->insert([
            'id' => '30',
            'title_fa' => 'همدان',
            'title_en' => 'Hamadan',
            'province_id' => '30'
        ]);

        // Yazd / Yazd
        DB::table('cities')->insert([
            'id' => '31',
            'title_fa' => 'یزد',
            'title_en' => 'Yazd',
            'province_id' => '31'
        ]);
    }
}
