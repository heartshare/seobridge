<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserNotification extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'type',
        'priority',
        'title',
        'message',
        'metadata',
        'has_been_opened',
        'opened_at',
    ];

    protected $casts = [
        'has_been_opened' => 'boolean',
        'opened_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected $attributes = [
        'metadata' => '{}',
        'has_been_opened' => false,
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
        return 'notification_'.Str::uuid();
    }
}
