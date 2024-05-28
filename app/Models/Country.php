<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'alpha_3',
        'name',
    ];



    public function prices()
    {
        return $this->morphedByMany(Price::class, 'model', 'model_has_countries');
    }

    public function taxes()
    {
        return $this->morphedByMany(Tax::class, 'model', 'model_has_countries');
    }
}
