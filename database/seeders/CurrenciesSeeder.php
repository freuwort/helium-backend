<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrenciesSeeder extends Seeder
{
    public const CURRENCIES = [
        ['code' => 'usd', 'name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2],
        ['code' => 'eur', 'name' => 'Euro', 'symbol' => '€', 'decimal_places' => 2],
        ['code' => 'jpy', 'name' => 'Japanese Yen', 'symbol' => '¥', 'decimal_places' => 0],
        ['code' => 'gbp', 'name' => 'Pound Sterling', 'symbol' => '£', 'decimal_places' => 2],
        ['code' => 'aud', 'name' => 'Australian Dollar', 'symbol' => '$', 'decimal_places' => 2],
        ['code' => 'cad', 'name' => 'Canadian Dollar', 'symbol' => '$', 'decimal_places' => 2],
        ['code' => 'chf', 'name' => 'Swiss Franc', 'symbol' => 'CHF', 'decimal_places' => 2],
        ['code' => 'cnh', 'name' => 'Chinese Renminbi', 'symbol' => '¥', 'decimal_places' => 2],
        ['code' => 'hkd', 'name' => 'Hong Kong Dollar', 'symbol' => '$', 'decimal_places' => 2],
        ['code' => 'nzd', 'name' => 'New Zealand Dollar', 'symbol' => '$', 'decimal_places' => 2],
    ];



    public function run(): void
    {
        foreach (self::CURRENCIES as $currency)
        {
            Currency::create($currency);
        }
    }
}
