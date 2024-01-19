<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];



    public static function getByKey($key)
    {
        return self::where('key', $key)->firstOrFail()->value;
    }



    public static function setSetting(string|array $key, $value = null): void
    {
        // If the key is an array
        if (is_array($key))
        {
            // Loop through the array and then set each key-value pair
            foreach ($key as $k => $v)
            {
                self::setSetting($k, $v);
            }

            return;
        }

        // Set the key-value pair
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
