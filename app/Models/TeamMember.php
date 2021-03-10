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
        'metadata',
    ];

    protected $casts = [
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



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}
