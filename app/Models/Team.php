<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Team extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'address',
        'category',
        'status',
        'payment_method_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
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
        return 'team_'.Str::uuid();
    }



    public function users()
    {
        return $this->belongsToMany(User::class, 'team_members', 'team_id', 'user_id', 'id', 'id');
    }

    public function members()
    {
        return $this->hasMany(TeamMember::class, 'team_id', 'id');
    }

    public function invites()
    {
        return $this->hasMany(TeamInvite::class, 'team_id', 'id');
    }
}
