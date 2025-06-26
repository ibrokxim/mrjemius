<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymeTransaction extends Model
{
    use HasFactory;
    protected $table = 'transactions'; // Указываем имя таблицы, если оно отличается от мн. числа имени модели

    protected $fillable = [
        'paycom_transaction_id', // ID транзакции от Payme
        'paycom_time',           // Время от Payme (строка timestamp_ms)
        'paycom_time_datetime',  // Время от Payme (формат DateTime)
        'create_time',           // Время создания на нашей стороне (timestamp_ms)
        'perform_time',          // Время проведения на нашей стороне (timestamp_ms)
        'cancel_time',           // Время отмены на нашей стороне (timestamp_ms) или от Payme
        'state',                 // Состояние транзакции (1 - создана, 2 - завершена, -1, -2 - отменена)
        'reason',                // Причина отмены (если есть)
        'receivers',             // Массив получателей (JSON, если используется) - пока не трогаем
        'order_id',              // ID вашего заказа
        'amount',                // Сумма в тиинах
        // 'owner_id' - не ясно, что это, возможно ID мерчанта или пользователя
        // 'transaction' - это поле из Payme `params.account.transaction` или `result.transaction`,
        //                которое обычно содержит ваш ID заказа. Если `order_id` уже есть, это может быть избыточно.
        //                Я буду использовать order_id для связи с заказом.
        // 'code' - не ясно, что это. Код ошибки?
        // 'payme_time' - дублирует 'paycom_time'?
        // 'perform_time_unix' - дублирует 'perform_time'?
    ];

    protected $casts = [
        'paycom_time_datetime' => 'datetime',
        'cancel_time' => 'datetime', // Если храните как datetime
        'state' => 'integer',
        'reason' => 'integer',
        'amount' => 'integer', // Сумма в тиинах
        'receivers' => 'array', // Если это JSON
    ];

    /**
     * Связь с заказом
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Константы для состояний транзакций Payme
    const STATE_CREATED = 1;
    const STATE_COMPLETED = 2;
    const STATE_CANCELLED = -1;
    const STATE_CANCELLED_AFTER_COMPLETE = -2; // Отмена после проведения (возврат)

    // Константы для причин отмены
    // (согласно документации Payme https://developer.help.paycom.uz/ru/metody-merchant-api/canceltransaction)
    const REASON_RECEIVER_NOT_FOUND = 1;         // Получатель платежа или один из получателей не найден или не активен (для выплат)
    const REASON_PROCESSING_ERROR = 2;           // Ошибка выполнения транзакции в биллинге мерчанта (например, недостаточно средств на счете получателя) (для выплат)
    const REASON_TRANSACTION_ERROR = 3;          // Ошибка выполнения транзакции (например, пользователь отменил транзакцию) (для платежей)
    const REASON_TIMEOUT = 4;                    // Таймаут при выполнении транзакции
    const REASON_REFUND = 5;                     // Возврат денег покупателю
    const REASON_UNKNOWN_ERROR = 10;
}
