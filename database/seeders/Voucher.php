<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Voucher extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'DISCOUNT10',
                'name' => 'Diskon 10% Max 10k',
                'is_public' => true,
                'voucher_type' => 'discount',
                'discount_cashback_type' => 'percentage',
                'discount_cashback_value' => 10,
                'discount_cashback_max' => 10000,
                'start_date' => now(),
                'end_date' => now()->addDays(7),
            ],
        ];

        foreach ($vouchers as $voucher) {
            if (rand(1, 10)) {
                $voucher['seller_id'] = null;
            } else {
                $voucher['seller_id'] = \App\Models\User::whereHas('products')
                    ->inRandomOrder()
                    ->first()
                    ->id;
            }

            \App\Models\Voucher::create($voucher);
        }

    }
}
