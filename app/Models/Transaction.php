<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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


}
