<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recomendations extends Model
{

    protected $table = 'recomendations';

    protected $fillable = [
        'name',
        'description',
        'calorie_range',
        'image_path',
        'image_color',
        'category', // Tambahkan kolom category
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope untuk filter berdasarkan kategori
    public function scopeByCategory($query, $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    // Scope untuk search berdasarkan nama
    public function scopeByName($query, $search)
    {
        if ($search) {
            return $query->where('name', 'LIKE', '%' . $search . '%');
        }
        return $query;
    }

    // Get unique categories
    public static function getCategories()
    {
        return self::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();
    }
}
