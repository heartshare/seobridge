<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeamMember extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'team_id',
        'roles',
        'invite_email',
        'metadata',
        'invited_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'metadata' => 'array',
        'roles' => 'array',
    ];

    protected $attributes = [
        'metadata' => '{}',
        'roles' => '[]',
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
        return 'member_'.Str::uuid();
    }
}
