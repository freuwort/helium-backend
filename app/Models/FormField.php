<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'form_id',
        'name',
        'key',
        'validation',
    ];

    protected $casts = [
        'validation' => 'array',
    ];



    // START: Relationships
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
    // END: Relationships
}
