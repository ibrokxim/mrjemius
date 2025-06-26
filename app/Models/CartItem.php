<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'session_id', // для гостевых пользователей
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Получить общую стоимость позиции корзины
     */
    public function getTotalPriceAttribute(): float
    {
        $price = $this->product->sell_price ?? $this->product->price;
        return $price * $this->quantity;
    }

    /**
     * Scope для активных товаров
     */
    public function scopeWithActiveProducts($query)
    {
        return $query->whereHas('product', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope для конкретного пользователя
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope для гостевой сессии
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId)->whereNull('user_id');
    }
}
