<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserReportGroup extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'owner_id',
        'team_id',
        'url',
        'host',
        'mode',
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
        return 'report_group_'.Str::uuid();
    }



    public function reports()
    {
        return $this->hasMany(UserReport::class, 'report_group_id', 'id');
    }

    public function task()
    {
        return $this->hasOne(UserReport::class, 'report_group_id', 'id');
    }
}
