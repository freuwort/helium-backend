<?php

namespace App\Classes\Data;

class Units
{
    public const ENTRIES = [
        ['code' => 'piece', 'name' => 'Piece', 'symbol' => '', 'type' => 'numeric'],
        ['code' => 'carton', 'name' => 'Carton', 'symbol' => '', 'type' => 'numeric'],
        
        ['code' => 'millimeter', 'name' => 'Millimeter', 'symbol' => 'mm', 'type' => 'length'],
        ['code' => 'centimeter', 'name' => 'Centimeter', 'symbol' => 'cm', 'type' => 'length'],
        ['code' => 'meter', 'name' => 'Meter', 'symbol' => 'm', 'type' => 'length'],
        ['code' => 'kilometer', 'name' => 'Kilometer', 'symbol' => 'km', 'type' => 'length'],
        ['code' => 'inch', 'name' => 'Inch', 'symbol' => 'in', 'type' => 'length'],
        ['code' => 'foot', 'name' => 'Foot', 'symbol' => 'ft', 'type' => 'length'],
        ['code' => 'yard', 'name' => 'Yard', 'symbol' => 'yd', 'type' => 'length'],
        ['code' => 'mile', 'name' => 'Mile', 'symbol' => 'mi', 'type' => 'length'],
        
        ['code' => 'square_meter', 'name' => 'Square Meter', 'symbol' => 'm²', 'type' => 'area'],
        ['code' => 'square_kilometer', 'name' => 'Square Kilometer', 'symbol' => 'km²', 'type' => 'area'],
        ['code' => 'square_inch', 'name' => 'Square Inch', 'symbol' => 'in²', 'type' => 'area'],
        ['code' => 'square_foot', 'name' => 'Square Foot', 'symbol' => 'ft²', 'type' => 'area'],
        ['code' => 'square_yard', 'name' => 'Square Yard', 'symbol' => 'yd²', 'type' => 'area'],
        
        ['code' => 'milliliter', 'name' => 'Milliliter', 'symbol' => 'ml', 'type' => 'volume'],
        ['code' => 'centiliter', 'name' => 'Centiliter', 'symbol' => 'cl', 'type' => 'volume'],
        ['code' => 'liter', 'name' => 'Liter', 'symbol' => 'l', 'type' => 'volume'],
        ['code' => 'cubic_millimeter', 'name' => 'Cubic Millimeter', 'symbol' => 'mm³', 'type' => 'volume'],
        ['code' => 'cubic_centimeter', 'name' => 'Cubic Centimeter', 'symbol' => 'cm³', 'type' => 'volume'],
        ['code' => 'cubic_meter', 'name' => 'Cubic Meter', 'symbol' => 'm³', 'type' => 'volume'],
        ['code' => 'cubic_kilometer', 'name' => 'Cubic Kilometer', 'symbol' => 'km³', 'type' => 'volume'],
        ['code' => 'cubic_inch', 'name' => 'Cubic Inch', 'symbol' => 'in³', 'type' => 'volume'],
        ['code' => 'cubic_foot', 'name' => 'Cubic Foot', 'symbol' => 'ft³', 'type' => 'volume'],

        ['code' => 'tonne', 'name' => 'Tonne', 'symbol' => 't', 'type' => 'mass'],
        ['code' => 'gram', 'name' => 'Gram', 'symbol' => 'g', 'type' => 'mass'],
        ['code' => 'kilogram', 'name' => 'Kilogram', 'symbol' => 'kg', 'type' => 'mass'],
        ['code' => 'pound', 'name' => 'Pound', 'symbol' => 'lb', 'type' => 'mass'],

        ['code' => 'hour', 'name' => 'Hour', 'symbol' => 'h', 'type' => 'time'],
        ['code' => 'minute', 'name' => 'Minute', 'symbol' => 'min', 'type' => 'time'],
        ['code' => 'second', 'name' => 'Second', 'symbol' => 's', 'type' => 'time'],

        ['code' => 'celsius', 'name' => 'Celsius', 'symbol' => '°C', 'type' => 'temperature'],
        ['code' => 'fahrenheit', 'name' => 'Fahrenheit', 'symbol' => '°F', 'type' => 'temperature'],
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