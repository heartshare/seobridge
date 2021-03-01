<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;

class User extends Authenticatable
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
        'roles',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
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
        return 'user_'.Str::uuid();
    }



    public function profile_image()
    {
        return $this->hasOne(UserImage::class, 'user_id', 'id');
    }

    public function reports()
    {
        return $this->hasMany(UserReport::class, 'user_id', 'id');
    }
}
