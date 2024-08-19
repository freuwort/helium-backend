<?php

namespace App\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PlaylistScreenPivot extends Pivot
{
    protected $casts = [
        'on_days' => 'array',
    ];

    protected function casts(): array
    {
        return [
            'from_date' => 'datetime:Y-m-d',
            'from_time' => 'datetime:H:i:s',
            'to_date' => 'datetime:Y-m-d',
            'to_time' => 'datetime:H:i:s',
        ];
    }
}