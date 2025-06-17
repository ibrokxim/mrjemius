<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'price_at_purchase',
        'total_price',
        'attributes',
    ];

    protected $casts = [
        'price_at_purchase' => 'decimal:2',
        'total_price' => 'decimal:2',
        'attributes' => 'array',
    ];

    // Заказ, к которому относится эта позиция.
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Товар этой позиции заказа.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
