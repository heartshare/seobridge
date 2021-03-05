<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserReport extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'report_group_id',
        'url',
        'host',
        'device',
        'score',
        'data',
        'metadata',
    ];

    protected $casts = [
        'device' => 'array',
        'data' => 'array',
        'metadata' => 'array',
    ];

    protected $attributes = [
        'device' => '{}',
        'data' => '{}',
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
        return 'report_'.Str::uuid();
    }
}
