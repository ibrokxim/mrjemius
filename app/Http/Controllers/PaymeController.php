<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TransactionResource;

class PaymeController extends Controller
{
    public function handle(Request $request)
    {
        Log::error($request->getContent());
        $req = json_decode($request->getContent(), true);

        // Проверяем, что декодирование прошло успешно и метод существует
        if (!isset($req['method'])) {
            return response()->json(['error' => ['code' => -32700, 'message' => 'Parse error']]);
        }

        $method = $req['method'];
        $params = $req['params'] ?? [];
        $id = $req['id'] ?? null;

        try {
            switch ($method) {
                case "CheckPerformTransaction":
                    return $this->checkPerformTransaction($params, $id);
                case "CreateTransaction":
                    return $this->createTransaction($params, $id);
                case "PerformTransaction":
                    return $this->performTransaction($params, $id);
                case "CancelTransaction":
                    return $this->cancelTransaction($params, $id);
                case "CheckTransaction":
                    return $this->checkTransaction($params, $id);
                case "GetStatement":
                    return $this->getStatement($params, $id);
                case "ChangePassword":
                    return $this->changePassword($params, $id);
                default:
                    return response()->json(['id' => $id, 'error' => ['code' => -32601, 'message' => [
                        'ru' => 'Метод не найден.',
                        'uz' => 'Metod topilmadi.',
                        'en' => 'Method not found.'
                    ]]]);
            }
        } catch (\Exception $e) {
            Log::error('Payme Global Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['id' => $id, 'error' => ['code' => -32400, 'message' => [
                'ru' => 'Системная ошибка.',
                'uz' => 'Tizim xatosi.',
                'en' => 'System error.'
            ]]]);
        }
    }

    private function checkPerformTransaction(array $params, $id)
    {
        if (empty($params['account']['order_id'])) {
            return response()->json(['id' => $id, 'error' => ['code' => -31050, 'message' =>
                [
                    'ru' => 'Не передан ID заказа.',
                    'uz' => 'Buyurtma IDsi berilmagan.',
                    'en' => 'Order ID is not provided.'
                ]
            ]]);
        }

        $order = Order::find($params['account']['order_id']);

        if (!$order) {
            return response()->json(['id' => $id, 'error' => ['code' => -31050, 'message' =>
                [
                    'ru' => 'Заказ не найден.',
                    'uz' => 'Buyurtma topilmadi.',
                    'en' => 'Order not found.'
                ]
            ]]);
        }
        if ($order->status !== 'pending') {
            return response()->json(['id' => $id, 'error' => ['code' => -31050, 'message' =>
                [
                    'ru' => 'Статус заказа не позволяет оплату.',
                    'uz' => 'Buyurtma holati to‘lovga ruxsat bermaydi.',
                    'en' => 'The order status does not allow payment.'
                ]
            ]]);
        }
        if ((int)($order->total_amount * 100 ) != (int)$params['amount']) {
            return response()->json(['id' => $id, 'error' => ['code' => -31001, 'message' =>
                [
                    'ru' => 'Неверная сумма.',
                    'uz' => 'Noto‘g‘ri summa.',
                    'en' => 'The amount does not match.'
                ]
            ]]);
        }
        $fiscalItems = [];
        foreach ($order->items as $item) {
            $product = $item->product;

            // Проверяем наличие обязательных фискальных полей у товара
            if (empty($product->ikpu_code) || empty($product->package_code)) {
                Log::error("Фискализация невозможна: у товара '{$product->name}' (ID: {$product->id}) отсутствуют фискальные данные.");
                return $this->sendErrorResponse(-32400, ['ru' => 'Ошибка в данных товара, фискализация невозможна.', 'uz' => 'Tovarlar maʼlumotlarida xatolik, fiskallashtirishning iloji yoʻq.', 'en' => 'Product data error, fiscalization is not possible.'], $id);
            }

            $fiscalItems[] = [
                'discount' => 0, // Установите значение, если у вас есть скидки на уровне позиции
                'title' => $product->getTranslation('name', 'ru'), // Название товара на русском
                'price' => ($item->price_at_purchase ?? $product->price) * 100, // Цена за единицу в тийинах
                'count' => $item->quantity,
                'code' => (string)$product->ikpu_code,           // <-- Используем новое поле
                'package_code' => (string)$product->package_code, // <-- Используем новое поле
                'vat_percent' => 12, // <-- Используем новое поле (должно быть числом)
                 //'units'    => $product->units_code,    // <-- Раскомментировать, если используете
            ];
        }

        return response()->json(['id' => $id, 'result' => [
            'allow' => true,
            'detail' => [
                'receipt_type' => 0,
                'items' => $fiscalItems,
            ]
        ]]);
    }

    private function createTransaction(array $params, $id)
    {
        // Сначала проводим все проверки
        // === НОВАЯ ПРОВЕРКА: Ищем ДРУГУЮ активную транзакцию для этого ЗАКАЗА ===
        $existingOrderTransaction = Transaction::where('order_id', $params['account']['order_id'])
            ->where('state', '!=', -1) // Игнорируем уже отмененные транзакции
            ->where('paycom_transaction_id', '!=', $params['id']) // Исключаем текущую транзакцию
            ->first();

        if ($existingOrderTransaction) {
            return response()->json(['id' => $id, 'error' => [
                'code' => -31050, // Используем код ошибки "неверный ввод 'account'"
                'message' => [
                    'ru' => 'Для данного заказа уже существует активная транзакция.',
                    'uz' => 'Ushbu buyurtma uchun faol tranzaksiya mavjud.',
                    'en' => 'An active transaction already exists for this order.'
                ],
                'data' => [
                    'field' => 'order_id'
                ]
            ]]);
        }
        $checkResponse = $this->checkPerformTransaction($params, $id);
        if (property_exists($checkResponse->getData(), 'error') && $checkResponse->getData()->error !== null) {
            return $checkResponse;
        }

        // Ищем транзакцию по ID от Payme.
        $transaction = Transaction::where('paycom_transaction_id', $params['id'])->first();

        if ($transaction) {
            // Если транзакция уже существует, проверяем ее состояние.
            if ($transaction->state != 1) { // Если она не в статусе "создана"
                return response()->json(['id' => $id, 'error' => ['code' => -31008, 'message' => [
                    'ru' => 'Неверный статус транзакции.',
                    'uz' => 'Tranzaksiya holati noto‘g‘ri.',
                    'en' => 'Invalid transaction state.'
                ]]]);
            }

            // Проверяем время жизни транзакции (12 часов = 43200 секунд)
            if (now()->diffInSeconds(Carbon::parse($transaction->paycom_time_datetime)) > 43200) {
                // Если просрочена, отменяем ее и возвращаем ошибку
                $transaction->state = -1;
                $transaction->reason = 4; // Код причины "Тайм-аут"
                $transaction->save();
                return response()->json(['id' => $id, 'error' => ['code' => -31008, 'message' => [
                    'ru' => 'Транзакция истекла.',
                    'uz' => 'Tranzaksiya muddati tugagan.',
                    'en' => 'Transaction expired.'
                ]]]);
            }

            // Если транзакция существует, в правильном статусе и не просрочена, возвращаем ее данные.
            return response()->json(['id' => $id, 'result' => [
                'create_time' => (int)$transaction->paycom_time,
                'transaction' => (string)$transaction->id,
                'state' => (int)$transaction->state
            ]]);
        }

        // Если транзакции еще не было, создаем новую
        $order = Order::find($params['account']['order_id']); // Мы уже уверены, что заказ существует

        $newTransaction = Transaction::create([
            'paycom_transaction_id' => $params['id'],
            'paycom_time' => $params['time'],
            'paycom_time_datetime' => Carbon::createFromTimestampMs($params['time']),
            'amount' => $params['amount'],
            'state' => 1, // Создана, в ожидании оплаты
            'order_id' => $params['account']['order_id'],
            'owner_id' => $order->user_id, // Сохраняем ID пользователя для удобства
        ]);

        // Возвращаем данные о новой транзакции
        return response()->json(['id' => $id, 'result' => [
            'create_time' => (int)$newTransaction->paycom_time,
            'transaction' => (string)$newTransaction->id,
            'state' => (int)$newTransaction->state
        ]]);
    }

    private function performTransaction(array $params, $id)
    {
        $transaction = Transaction::where('paycom_transaction_id', $params['id'])->first();
        if (!$transaction) {
            return response()->json(['id' => $id, 'error' => ['code' => -31003, 'message' => [
                'ru' => 'Транзакция не найдена.',
                'uz' => 'Tranzaksiya topilmadi.',
                'en' => 'Transaction not found.'
            ]]]);
        }
        if ($transaction->state == 2) {
            return response()->json(['id' => $id, 'result' => ['transaction' => (string)$transaction->id, 'perform_time' => (int)$transaction->perform_time_unix, 'state' => (int)$transaction->state]]);
        }
        if ($transaction->state != 1) {
            return response()->json(['id' => $id, 'error' => ['code' => -31008, 'message' => [
                'ru' => 'Неверный статус транзакции.',
                'uz' => 'Tranzaksiya holati noto‘g‘ri.',
                'en' => 'Invalid transaction state.'
            ]]]);
        }

        $transaction->state = 2;
        $transaction->perform_time_unix = Carbon::now()->timestamp * 1000;
        $transaction->perform_time = now();
        $transaction->save();

        Log::info("Payme: Транзакция {$transaction->id} проведена успешно. Ищем заказ...");

        $order = $transaction->order;
        if ($order) {
            Log::info("Payme: Найден заказ #{$order->id} для транзакции {$transaction->id}. Обновляем статус.");
            $order->update([
                'status' => 'processing',
                'payment_status' => 'paid']);
            Log::info("Payme: Статус заказа #{$order->id} обновлен.");
            try {
                (new TelegramService())->sendOrderNotifications($order);
                Log::info("Payme: Уведомление для заказа #{$order->id} отправлено.");
            } catch (\Exception $e) {
                Log::error("Ошибка отправки Telegram для заказа {$order->id}: " . $e->getMessage());
            }
        }

        return response()->json(['id' => $id, 'result' => [
            'transaction' => (string)$transaction->id,
            'perform_time' => (int)$transaction->perform_time_unix,
            'state' => (int)$transaction->state]]);
    }
    private function checkTransaction(array $params, $id)
    {
        // 1. Ищем транзакцию по ID от Payme
        $transaction = Transaction::where('paycom_transaction_id', $params['id'])->first();

        // 2. Если транзакция не найдена
        if (!$transaction) {
            return response()->json(['id' => $id, 'error' => [
                'code' => -31003,
                'message' => [
                    'ru' => 'Транзакция не найдена.',
                    'uz' => 'Tranzaksiya topilmadi.',
                    'en' => 'Transaction not found.'
                ]
            ]]);
        }

        // 3. Если найдена, возвращаем ее данные в формате, который ожидает Payme
        return response()->json(['id' => $id, 'result' => [
            'create_time' => (int)$transaction->paycom_time,
            'perform_time' => (int)($transaction->perform_time_unix ?? 0), // 0 если еще не выполнена
            'cancel_time' => $transaction->cancel_time ? (Carbon::parse($transaction->cancel_time)->timestamp * 1000) : 0, // 0 если не отменена
            'transaction' => (string)$transaction->id,
            'state' => (int)$transaction->state,
            'reason' => isset($transaction->reason) ? (int)$transaction->reason : null, // Причина отмены, если есть
        ]]);
    }
    private function cancelTransaction(array $params, $id)
    {
        // 1. Ищем транзакцию
        $transaction = Transaction::where('paycom_transaction_id', $params['id'])->first();
        if (!$transaction) {
            return response()->json(['id' => $id, 'error' => [
                'code' => -31003,
                'message' => [
                    'ru' => 'Транзакция не найдена.',
                    'uz' => 'Tranzaksiya topilmadi.',
                    'en' => 'Transaction not found.'
                ]
            ]]);
        }

        // 2. Определяем новый статус и время отмены
        $cancellationTime = now();

        if ($transaction->state == 1) { // Если была в ожидании
            $transaction->state = -1; // Становится "отменена"
        } elseif ($transaction->state == 2) { // Если была успешной (нужно сделать возврат)
            // ВАЖНО: Здесь должна быть ваша бизнес-логика возврата денег,
            // если это необходимо. Сейчас мы просто меняем статус.
            $transaction->state = -2; // Становится "отменена после выполнения"
        } else { // Если уже была отменена (-1 или -2)
            // Ничего не делаем, просто возвращаем текущее состояние
        }

        // 3. Сохраняем изменения в транзакции
        $transaction->reason = $params['reason'];
        if (!$transaction->cancel_time) { // Записываем время отмены, только если его еще нет
            $transaction->cancel_time = $cancellationTime;
        }
        $transaction->save();

        // 4. Обновляем статус заказа, если он еще не в финальном статусе
        $order = $transaction->order;
        if ($order && !in_array($order->status, ['delivered', 'cancelled'])) {
            $order->status = 'cancelled';
            $order->save();
        }

        // 5. Возвращаем успешный ответ
        return response()->json(['id' => $id, 'result' => [
            'transaction' => (string)$transaction->id,
            'cancel_time' => Carbon::parse($transaction->cancel_time)->timestamp * 1000,
            'state' => (int)$transaction->state,
        ]]);
    }
    private function getStatement(array $params, $id)
    {
        if (empty($params['from']) || empty($params['to'])) {
            return response()->json([
                'id' => $id,
                'error' => [
                    'code' => -32602,
                    'message' => [
                        'ru' => 'Параметры from и to обязательны.',
                        'uz' => '"from" va "to" parametrlari majburiy.',
                        'en' => 'Parameters "from" and "to" are required.'
                    ]
                ]
            ]);
        }

        $transactions = Transaction::getTransactionsByTimeRange($params['from'], $params['to']);

        return response()->json([
            'id' => $id,
            'result' => [
                'transactions' => TransactionResource::collection($transactions),
            ]
        ]);
    }

    private function changePassword(array $params, $id)
    {
        return response()->json([
            'id' => $id,
            'error' => [
                'code' => -32504,
                'message' => [
                    'ru' => 'Недостаточно привилегий для выполнения метода.',
                    'uz' => 'Ushbu metodni bajarish uchun yetarli huquqlar mavjud emas.',
                    'en' => 'Insufficient privileges to perform the method.'
                ]
            ]
        ]);
    }

}
