<?php

namespace App\Classes\Data;

class Currencies
{
    public const ENTRIES = [
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

    public static function get()
    {
        return self::ENTRIES;
    }

    public static function getCodes()
    {
        return collect(self::ENTRIES)->pluck('code')->toArray();
    }
}