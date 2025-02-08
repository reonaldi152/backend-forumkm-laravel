<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            'dummy/Banner-1.png',
            'dummy/Banner-2.png',
            'dummy/Banner-3.png',
            'dummy/Banner-4.png',
        ];

        foreach ($sliders as $slider) {
            \App\Models\Slider::create([
                'image' => $slider
            ]);
        }
    }
}
