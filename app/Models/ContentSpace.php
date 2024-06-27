<?php

namespace App\Models;

use App\Traits\HasAccessControl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentSpace extends Model
{
    use HasFactory, HasAccessControl;

    protected $fillable = [
        'parent_id',
        'inherit_access',
        'owner_id',
        'name',
    ];

    protected $casts = [
        'inherit_access' => 'boolean',
    ];



    // START: Relationships
    public function parent()
    {
        return $this->belongsTo(ContentSpace::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ContentSpace::class, 'parent_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
    // END: Relationships
}
