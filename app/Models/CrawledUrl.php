<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawledUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'url',
        'found_on_domain',
        'found_on_url',
    ];
}
