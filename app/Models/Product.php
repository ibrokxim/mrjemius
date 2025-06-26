<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;
    public array $translatable = [
        'name',
        'description',
        'short_description',

        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
    ];

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'sell_price',
        'stock_quantity',
        'is_active',
        'is_featured',
        'weight_kg',
        'attributes',
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
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight_kg' => 'decimal:3',
        'attributes' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlistedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlist_items');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function cartedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'cart_items');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class, 'promotion_product');
    }

    public function seoMetas(): MorphMany
    {
        return $this->morphMany(SeoMeta::class, 'model');
    }

    public function getCurrentLocaleSeoMeta()
    {
        return $this->seoMetas()->where('locale', app()->getLocale())->first();
    }

}
