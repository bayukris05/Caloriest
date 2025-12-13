<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'views_count',
        'likes_count',
        'answers_count'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships dengan fallback
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Deleted User',
            'avatar' => 'images/default-avatar.png'
        ]);
    }

    public function answers()
    {
        return $this->hasMany(ForumAnswer::class, 'post_id');
    }

    public function likes()
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    // Scopes
    public function scopeByTimeRange($query, $timeRange)
    {
        switch ($timeRange) {
            case 'today':
                return $query->whereDate('created_at', today());
            case 'week':
                return $query->where('created_at', '>=', now()->subWeek());
            case 'month':
                return $query->where('created_at', '>=', now()->subMonth());
            case 'year':
                return $query->where('created_at', '>=', now()->subYear());
            default:
                return $query;
        }
    }

    public function scopeWithStats($query)
    {
        return $query->withCount(['answers', 'likes']);
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
        return $this;
    }

    public function isLikedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isLikedByCurrentUser()
    {
        if (Auth::check()) {
            return $this->likes()->where('user_id', Auth::id())->exists();
        }
        $session_id = session()->getId();
        return $this->likes()->where('session_id', $session_id)->exists();
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('F j, Y');
    }

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