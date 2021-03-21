<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'url',
        'title',
        'intro_image',
        'intro_text',
        'full_text',
        'author_id',
        'category_id',
        'metadata',
        'published_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'published_at' => 'datetime',
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
        return 'article_'.Str::uuid();
    }

    public function author()
    {
        return $this->hasOne(AuthorProfile::class, 'id', 'author_id');
    }

    public function category()
    {
        return $this->hasOne(ArticleCategory::class, 'id', 'category_id');
    }
}
