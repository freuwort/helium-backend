<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'model_id',
        'model_type',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];



    // START: Relationships
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
    // END: Relationships
}
