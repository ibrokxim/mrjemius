<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['order_number',
        'user_id',
        'guest_email',
        'shipping_address_id',
        'billing_address_id',
        'status',
        'subtotal_amount',
        'discount_amount',
        'shipping_amount',
        'tax_amount',
        'total_amount',
        'loyalty_points_earned',
        'loyalty_points_spent',
        'loyalty_points_discount_amount',
        'payment_method',
        'payment_status',
        'transaction_id',
        'customer_notes',
        'admin_notes',
        'shipped_at',
        'delivered_at', ];

    protected $casts = [
        'subtotal_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'loyalty_points_discount_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Shipping address
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    // Billing address
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    // Products in order
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function loyaltyPointsTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyPointsTransaction::class);
    }

    // Order applied promotions
    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'order_promotion')
            ->withPivot('discount_applied');
    }


}
