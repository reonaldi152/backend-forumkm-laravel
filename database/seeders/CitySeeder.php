<?php

namespace Database\Seeders;

use App\Models\Address\City;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Daftar kota untuk setiap provinsi (contoh)
        $cities = [
            ['province_id' => 1, 'external_id' => 1, 'name' => 'Banda Aceh'],
            ['province_id' => 2, 'external_id' => 2, 'name' => 'Denpasar'],
            ['province_id' => 3, 'external_id' => 3, 'name' => 'Jakarta Selatan'],
            // Tambahkan kota lainnya sesuai kebutuhan
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
