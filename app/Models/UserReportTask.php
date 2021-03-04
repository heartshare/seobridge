<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserReportTask extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'url',
        'status',
        'progress',
        'progress_max',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $attributes = [
        'metadata' => '{}',
        'progress' => 0,
        'progress_max' => 0,
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
        return 'job_'.Str::uuid();
    }
}
