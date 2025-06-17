<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPointsTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'type',
        'points',
        'description',
        'expires_at',
    ];

    protected $casts = [
        'points' => 'integer',
        'expires_at' => 'datetime',
    ];

    /**
     * Пользователь, с чьим счетом связана транзакция.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Заказ, по которому была совершена транзакция (если применимо).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
