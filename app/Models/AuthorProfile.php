<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuthorProfile extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'url',
        'full_name',
        'display_name',
        'image',
        'biography',
        'social_links',
        'metadata',
        'verified_at',
    ];

    protected $casts = [
        'social_links' => 'array',
        'metadata' => 'array',
        'verified_at' => 'datetime',
    ];

    protected $attributes = [
        'social_links' => '[]',
        'metadata' => '{}',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->id = self::generateUuid();
        });
    }

    public static function generateUuid()
    {
        return 'author_'.Str::uuid();
    }
}
