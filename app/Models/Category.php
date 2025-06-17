<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'image_url',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image_url',
        'canonical_url',
        'robots_tags',
        'custom_html_head_start',
        'custom_html_head_end',
        'custom_html_body_start',
        'custom_html_body_end'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): belongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): hasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
