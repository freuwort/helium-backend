<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];



    // START: Relationships
    public function fields()
    {
        return $this->hasMany(FormField::class);
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }
    // END: Relationships



    public function getValidationRules()
    {
        $rules = [];
        
        $this->fields()->get(['key', 'validation'])->each(function ($field) use (&$rules) {
            $rules[$field->key] = $field->validation;
        });

        return $rules;
    }
}
