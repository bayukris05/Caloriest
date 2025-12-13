<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'content',
        'likes_count',
        'is_best_answer'
    ];

    protected $casts = [
        'is_best_answer' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Deleted User',
            'avatar' => 'images/default-avatar.png'
        ]);
    }

    public function likes()
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    public function isLikedByAuthUser()
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->likes()->where('user_id', auth()->id())->exists();
    }

    public function isLikedByCurrentUser()
    {
        if (auth()->check()) {
            return $this->likes()->where('user_id', auth()->id())->exists();
        }
        $session_id = session()->getId();
        return $this->likes()->where('session_id', $session_id)->exists();
    }

    // app/Models/ForumAnswer.php
    public function getAvatarUrlAttribute()
    {
        if ($this->user && $this->user->avatar) {
            return asset('storage/' . $this->user->avatar);
        }
        return asset('images/default-avatar.png');
    }

    public function getSafeUserAttribute()
    {
        if ($this->user) {
            return $this->user;
        }
        return (object) [
            'id' => 0,
            'name' => 'Deleted User',
            'avatar' => 'images/default-avatar.png',
            'avatar_url' => asset('images/default-avatar.png')
        ];
    }
}