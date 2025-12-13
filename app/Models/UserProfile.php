<?php

namespace App\Models;

use Illuminate\Database\Model\Eloquent;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'usia',
        'jenis_kelamin',
        'tb',
        'bb',
        'aktivitas',
        'id_user',
        'image_path'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
