<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeamInvite extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'team_id',
        'user_id',
        'email',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $attributes = [
        'status' => 'OK',
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
        return 'invite_'.Str::uuid();
    }
}
