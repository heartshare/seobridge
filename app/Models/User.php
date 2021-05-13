<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes, Billable;

    public $incrementing = false;

    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
        'metadata',
        'totp_mfa_seed',
        'is_mfa_enabled',
        'active_team_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'totp_mfa_seed',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
        'metadata' => 'array',
        'is_mfa_enabled' => 'boolean',
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
        return 'user_'.Str::uuid();
    }



    public function profile_image()
    {
        return $this->hasOne(UserImage::class, 'user_id', 'id');
    }

    public function report_groups()
    {
        return $this->hasMany(UserReportGroup::class, 'user_id', 'id');
    }

    public function mfa_methods()
    {
        return $this->hasMany(UserMFAMethod::class, 'user_id', 'id');
    }
    
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members', 'user_id', 'team_id', 'id', 'id');
    }

    public function active_team()
    {
        return $this->hasOne(Team::class, 'id', 'active_team_id');
    }

    public function author_profile()
    {
        return $this->hasOne(AuthorProfile::class, 'user_id', 'id');
    }
}
