<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReportGroupShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'report_group_id',
        'team_id',
        'user_id',
        'is_assigned',
        'assigned_to_user_id',
        'metadata',
        'completed_at',
    ];

    protected $casts = [
        'is_assigned' => 'boolean',
        'metadata' => 'array',
        'completed_at' => 'datetime',
    ];

    protected $attributes = [
        'is_assigned' => false,
        'metadata' => '{}',
    ];
}
