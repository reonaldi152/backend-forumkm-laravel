<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Daftar provinsi Indonesia (contoh)
        $provinces = [
            ['external_id' => 1, 'name' => 'Aceh'],
            ['external_id' => 2, 'name' => 'Bali'],
            ['external_id' => 3, 'name' => 'Jakarta'],
            // Tambahkan provinsi lain sesuai kebutuhan
        ];

        foreach ($provinces as $province) {
            Province::create($province);
        }
    }
}
