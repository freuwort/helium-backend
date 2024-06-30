<?php

namespace App\Models;

use App\Traits\HasAccessControl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasAccessControl;

    protected $fillable = [
        'parent_id',
        'inherit_access',
        'owner_id',
        'type',
        'name',
        'slug',
        'content',
        'icon',
        'color',
        'hidden',
    ];

    protected $casts = [
        'inherit_access' => 'boolean',
    ];



    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            $model->owner()->associate(auth()->user())->save();
        });
    }



    // START: Relationships
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function product_groups()
    {
        return $this->morphedByMany(ProductGroup::class, 'model', 'model_has_category');
    }
    // END: Relationships
}
