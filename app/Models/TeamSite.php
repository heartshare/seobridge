<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeamSite extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'team_id',
        'host',
        'is_wildcard',
        'metadata',
    ];

    protected $casts = [
        'is_wildcard' => 'boolean',
        'metadata' => 'array',
    ];

    protected $attributes = [
        'is_wildcard' => false,
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
        return 'team_site_'.Str::uuid();
    }
}
