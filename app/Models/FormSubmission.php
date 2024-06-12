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
        'files',
    ];

    protected $casts = [
        'data' => 'array',
        'files' => 'array',
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



    public static function boot()
    {
        parent::boot();

        self::deleting(function ($model) {
            collect($model->files)->flatten()->each(function ($file) {
                $media = Media::firstWhere('src_path', $file);
                if ($media) $media->delete();
            });
        });
    }
}
