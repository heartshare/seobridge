<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMFAMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'token',
        'name',
        'is_verified',
        'metadata',
    ];

    protected $hidden = [
        'token',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_verified' => 'boolean',
    ];

    protected $attributes = [
        'metadata' => '{}',
    ];
}
