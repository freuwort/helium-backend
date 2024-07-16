<?php

namespace App\Models;

use App\Traits\HasAccessControl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentPostGroup extends Model
{
    use HasFactory, HasAccessControl;

    public $timestamps = false;

    protected $fillable = [
        'space_id',
        'post_id',
        'owner_id',
        'hidden',
    ];

    protected $casts = [
        'hidden' => 'boolean',
    ];



    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            $model->owner()->associate(auth()->user())->save();
        });
    }



    // START: Relationships
    public function space()
    {
        return $this->belongsTo(ContentSpace::class, 'space_id');
    }

    public function post()
    {
        return $this->belongsTo(ContentPost::class, 'post_id');
    }

    public function draft()
    {
        return $this->hasOne(ContentPost::class, 'group_id')->where('type', 'draft');
    }

    public function posts()
    {
        return $this->hasMany(ContentPost::class, 'group_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
    // END: Relationships



    public function approveDraft()
    {
        $post = $this->postFromDraft();
        $this->selectRevision($post);
        $this->resetDraft();
    }

    public function postFromDraft()
    {
        $draft = $this->draft()->first()->replicate();
        $draft->type = 'post';
        $draft->approved_at = now();
        $draft->save();

        return $draft;
    }

    public function resetDraft()
    {
        $this->draft()->update([
            'approved_at' => null,
            'review_ready' => false,
        ]);
    }

    public function selectRevision(ContentPost|int $post)
    {
        $this->update([
            'post_id' => $post instanceof ContentPost ? $post->id : $post,
        ]);
    }
}
