<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * currencies with sample reference rates.
     */
    public function run(): void
    {
        $currencies = [
            ['name' => 'USD', 'symbol' => '$', 'exchange_rate' => 1],
            ['name' => 'EUR', 'symbol' => '€', 'exchange_rate' => 0.92],
            ['name' => 'COP', 'symbol' => '$', 'exchange_rate' => 4100],
            ['name' => 'MXN', 'symbol' => '$', 'exchange_rate' => 18.5],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(['name' => $currency['name']], $currency);
        }
    }
}
