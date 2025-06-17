<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'max_uses',
        'max_uses_user',
        'uses_count',
        'starts_at',
        'expires_at',
        'minimum_spend',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_spend' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Товары, к которым применима эта акция (если акция на конкретные товары).
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promotion_product');
    }

    /**
     * Заказы, к которым была применена эта акция.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_promotion')
            ->withPivot('discount_applied');
    }
}
