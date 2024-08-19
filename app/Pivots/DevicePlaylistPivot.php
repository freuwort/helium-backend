<?php

namespace App\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DevicePlaylistPivot extends Pivot
{
    protected $casts = [
        'from_date' => 'datetime:Y-m-d',
        'from_time' => 'datetime:H:i:s',
        'to_date' => 'datetime:Y-m-d',
        'to_time' => 'datetime:H:i:s',
        'on_days' => 'array',
    ];
}