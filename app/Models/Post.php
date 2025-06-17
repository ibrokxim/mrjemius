<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image_url',
        'status',
        'published_at', ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Автор поста.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Категории, к которым относится пост.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(PostCategory::class, 'post_post_category');
    }
}
