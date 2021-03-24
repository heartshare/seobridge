<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'url',
        'stripe_price_id',
        'cost',
        'name',
        'image',
        'description',
        'metadata',
        'valid_until',
    ];

    protected $casts = [
        'metadata' => 'array',
        'valid_until' => 'datetime',
    ];

    protected $attributes = [
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
        return 'plan_'.Str::uuid();
    }
}
