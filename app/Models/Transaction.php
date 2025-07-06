<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    protected $casts = [
        // Eloquent будет автоматически конвертировать эти колонки в объекты Carbon
        'paycom_time_datetime' => 'datetime',
        'cancel_time' => 'datetime',

        // Также полезно преобразовать и другие поля для удобства
        'amount' => 'integer', // Если это тийины, integer подходит
        'state' => 'integer',
        'reason' => 'integer',
    ];

    public static function getTransactionsByTimeRange($from, $to)
    {
        return self::whereBetween('paycom_time', [$from, $to])
            ->where('state', '>=', 1) // Только успешные/в процессе транзакции
            ->orderBy('paycom_time')
            ->get();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}
