<?php

namespace App\Http\Controllers;

use App\Exceptions\PaymeException;
use App\Models\Order;
use App\Models\PaymeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymeController extends Controller
{
    protected string $kassaKey;
    protected const PAYME_LOGIN = 'Paycom'; // Логин для Basic Auth от Payme

    public function webhook(Request $request)
    {
        Log::info('Payme webhook called', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        return response()->json([
            'result' => [
                'message' => 'Webhook is working'
            ]
        ]);
    }
//    public function __construct()
//    {
//        $this->kassaKey = config('payme.kassa_key_for_callback');
//        if (empty($this->kassaKey)) {
//            Log::critical('Payme Kassa Key for callback is not configured!');
//            // В реальном приложении это должно быть обработано более строго
//        }
//    }
//
//    /**
//     * Обработчик всех RPC запросов от Payme
//     */
//    public function handle(Request $request)
//    {
//        $payload = $request->all();
//        Log::channel('payme')->info('Payme RPC Request:', $payload); // Пишем в отдельный лог-канал 'payme'
//
//        // 1. Аутентификация
//        $authHeader = $request->header('Authorization');
//        $expectedHeader = 'Basic ' . base64_encode(self::PAYME_LOGIN . ':' . $this->kassaKey);
//
//        if (!$authHeader || !hash_equals($expectedHeader, $authHeader)) {
//            Log::channel('payme')->warning('Payme RPC: Invalid Authorization.', ['received' => $authHeader]);
//            return $this->errorResponse($payload['id'] ?? null, -32504, 'Недостаточно прав для выполнения данной операции (неверный токен авторизации).');
//        }
//
//        // 2. Валидация JSON-RPC структуры
//        if (!isset($payload['method']) || !isset($payload['id']) || !array_key_exists('params', $payload)) {
//            Log::channel('payme')->warning('Payme RPC: Invalid JSON-RPC structure.', $payload);
//            return $this->errorResponse($payload['id'] ?? null, -32600, 'Невалидный запрос.');
//        }
//
//        $method = $payload['method'];
//        $params = $payload['params'];
//        $requestId = $payload['id'];
//
//        try {
//            switch ($method) {
//                case 'CheckPerformTransaction':
//                    return $this->checkPerformTransaction($params, $requestId);
//                case 'CreateTransaction':
//                    return $this->createTransaction($params, $requestId);
//                case 'PerformTransaction':
//                    return $this->performTransaction($params, $requestId);
//                case 'CancelTransaction':
//                    return $this->cancelTransaction($params, $requestId);
//                case 'CheckTransaction':
//                    return $this->checkTransaction($params, $requestId);
//                // case 'GetStatement':
//                //     return $this->getStatement($params, $requestId);
//                default:
//                    return $this->errorResponse($requestId, -32601, 'Метод не найден.');
//            }
//        } catch (PaymeException $e) {
//            Log::channel('payme')->error('Payme RPC Exception:', [
//                'method' => $method, 'id' => $requestId, 'params' => $params,
//                'code' => $e->getPaymeErrorCode(), 'message' => $e->getMessageForPayme(), 'data' => $e->getPaymeErrorData()
//            ]);
//            return $this->errorResponse($requestId, $e->getPaymeErrorCode(), $e->getMessageForPayme(), $e->getPaymeErrorData());
//        } catch (\Exception $e) {
//            Log::channel('payme')->critical('Payme RPC Unhandled Exception:', [
//                'method' => $method, 'id' => $requestId, 'params' => $params,
//                'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()
//            ]);
//            return $this->errorResponse($requestId, -32400, 'Системная ошибка.');
//        }
//    }
//
//    // --- Реализация RPC методов ---
//
//    protected function checkPerformTransaction(array $params, $requestId)
//    {
//        $validator = Validator::make($params, [
//            'amount' => 'required|integer|min:1',
//            'account.order_id' => 'required', // или другой ваш идентификатор
//        ]);
//        if ($validator->fails()) {
//            throw new PaymeException('Неверные параметры.', -31050, $validator->errors()->first());
//        }
//
//        $orderId = $params['account']['order_id'];
//        $amount = $params['amount'];
//
//        $order = Order::find($orderId);
//        if (!$order) {
//            throw new PaymeException(['ru' => 'Заказ не найден', 'uz' => 'Buyurtma topilmadi'], -31050, 'order_not_found');
//        }
//        if ((int)round($order->total_amount * 100) !== $amount) {
//            throw new PaymeException('Неверная сумма.', -31001);
//        }
//        if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_PAYMENT_FAILED])) { // Пример статусов
//            throw new PaymeException('Статус заказа не позволяет оплату.', -31051, 'order_state_invalid');
//        }
//        // Дополнительные проверки, если нужны
//
//        return $this->successResponse($requestId, ['allow' => true]);
//    }
//
//    protected function createTransaction(array $params, $requestId)
//    {
//        $validator = Validator::make($params, [
//            'id' => 'required|string|max:25', // Payme transaction ID
//            'time' => 'required|numeric',      // Payme time (ms)
//            'amount' => 'required|integer|min:1',
//            'account.order_id' => 'required',
//        ]);
//        if ($validator->fails()) {
//            throw new PaymeException('Неверные параметры.', -31050, $validator->errors()->first());
//        }
//
//        $paymeTransactionId = $params['id'];
//        $paymeTimeMs = $params['time'];
//        $orderId = $params['account']['order_id'];
//        $amount = $params['amount'];
//
//        $order = Order::find($orderId);
//        // Повторные проверки, как в CheckPerformTransaction
//        if (!$order || (int)round($order->total_amount * 100) !== $amount || !in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_PAYMENT_FAILED])) {
//            throw new PaymeException('Заказ не может быть обработан.', -31050, 'order_validation_failed');
//        }
//
//        return DB::transaction(function () use ($order, $paymeTransactionId, $paymeTimeMs, $amount, $requestId) {
//            $transaction = PaymeTransaction::where('paycom_transaction_id', $paymeTransactionId)->first();
//
//            if ($transaction) {
//                if ($transaction->state != PaymeTransaction::STATE_CREATED) {
//                    throw new PaymeException('Состояние транзакции не позволяет ее создать повторно.', -31008);
//                }
//                // Уже создана, возвращаем ее данные
//            } else {
//                $transaction = PaymeTransaction::create([
//                    'paycom_transaction_id' => $paymeTransactionId,
//                    'paycom_time' => (string)$paymeTimeMs,
//                    'paycom_time_datetime' => \Carbon\Carbon::createFromTimestampMs($paymeTimeMs),
//                    'create_time' => (string)(time() * 1000), // Наше время создания
//                    'state' => PaymeTransaction::STATE_CREATED,
//                    'order_id' => $order->id,
//                    'amount' => $amount,
//                ]);
//                // Обновляем статус заказа
//                $order->status = Order::STATUS_PENDING_PAYMENT; // Ваш статус "Ожидает оплаты Payme"
//                $order->save();
//            }
//
//            return $this->successResponse($requestId, [
//                'create_time' => (int)$transaction->create_time, // Документация Payme ожидает int (ms)
//                'transaction' => (string)$transaction->id, // ID НАШЕЙ транзакции
//                'state' => (int)$transaction->state,
//            ]);
//        });
//    }
//
//    protected function performTransaction(array $params, $requestId)
//    {
//        $validator = Validator::make($params, ['id' => 'required|string|max:25']);
//        if ($validator->fails()) {
//            throw new PaymeException('Неверные параметры.', -31050, $validator->errors()->first());
//        }
//        $paymeTransactionId = $params['id'];
//
//        return DB::transaction(function () use ($paymeTransactionId, $requestId) {
//            $transaction = PaymeTransaction::where('paycom_transaction_id', $paymeTransactionId)->first();
//
//            if (!$transaction) {
//                throw new PaymeException('Транзакция не найдена.', -31003);
//            }
//
//            if ($transaction->state == PaymeTransaction::STATE_COMPLETED) {
//                // Уже завершена
//            } elseif ($transaction->state == PaymeTransaction::STATE_CREATED) {
//                // Проверяем таймаут (12 часов = 43 200 000 мс)
//                $currentTimeMs = time() * 1000;
//                if (($currentTimeMs - (int)$transaction->paycom_time) > 43200000) {
//                    $transaction->state = PaymeTransaction::STATE_CANCELLED_AFTER_COMPLETE; // Или другой код для таймаута
//                    $transaction->reason = PaymeTransaction::REASON_TIMEOUT;
//                    $transaction->save();
//                    throw new PaymeException('Таймаут транзакции.', -31008, 'timeout');
//                }
//
//                // Проводим транзакцию
//                $transaction->state = PaymeTransaction::STATE_COMPLETED;
//                $transaction->perform_time = (string)$currentTimeMs;
//                $transaction->save();
//
//                // Обновляем заказ
//                $order = $transaction->order;
//                if ($order) {
//                    $order->status = Order::STATUS_PAID; // Ваш статус "Оплачен"
//                    $order->paid_at = now();
//                    $order->save();
//                    // TODO: Отправка уведомлений, фискализация (если нужна на этом этапе)
//                    // $paymeService = resolve(PaymeService::class);
//                    // $paymeService->sendReceiptToFiscalModule(...)
//                }
//            } else { // Неверное состояние для проведения
//                throw new PaymeException('Невозможно провести транзакцию в текущем состоянии.', -31008);
//            }
//
//            return $this->successResponse($requestId, [
//                'perform_time' => (int)$transaction->perform_time,
//                'transaction' => (string)$transaction->id,
//                'state' => (int)$transaction->state,
//            ]);
//        });
//    }
//
//    protected function cancelTransaction(array $params, $requestId)
//    {
//        $validator = Validator::make($params, [
//            'id' => 'required|string|max:25',
//            'reason' => 'required|integer',
//        ]);
//        if ($validator->fails()) {
//            throw new PaymeException('Неверные параметры.', -31050, $validator->errors()->first());
//        }
//
//        $paymeTransactionId = $params['id'];
//        $reason = $params['reason'];
//
//        return DB::transaction(function () use ($paymeTransactionId, $reason, $requestId) {
//            $transaction = PaymeTransaction::where('paycom_transaction_id', $paymeTransactionId)->first();
//
//            if (!$transaction) {
//                throw new PaymeException('Транзакция не найдена.', -31003);
//            }
//
//            $cancelTime = (string)(time() * 1000);
//
//            if ($transaction->state == PaymeTransaction::STATE_CREATED) {
//                $transaction->state = PaymeTransaction::STATE_CANCELLED;
//            } elseif ($transaction->state == PaymeTransaction::STATE_COMPLETED) {
//                // Отмена УЖЕ ПРОВЕДЕННОЙ транзакции (возврат)
//                // Для этого обычно используется receipts.cancel с причиной возврата (5)
//                // или отдельный процесс возврата в Payme.
//                // Если это отмена до клиринга, то возможно -2.
//                // Здесь нужно уточнить бизнес-логику. Если это отмена по инициативе мерчанта после оплаты,
//                // то это скорее возврат, и Payme может иметь для этого другой механизм.
//                // Пока предполагаем, что если причина 5, то это возврат.
//                if ($reason == PaymeTransaction::REASON_REFUND) {
//                    $transaction->state = PaymeTransaction::STATE_CANCELLED_AFTER_COMPLETE;
//                } else {
//                    // Нельзя отменить успешно проведенную транзакцию обычной отменой
//                    throw new PaymeException('Невозможно отменить уже успешно проведенную транзакцию этим методом.', -31007);
//                }
//
//            } elseif ($transaction->state < 0) { // Уже отменена
//                // Возвращаем текущее состояние отмены
//            } else {
//                throw new PaymeException('Невозможно отменить транзакцию в текущем состоянии.', -31008);
//            }
//
//            $transaction->cancel_time = $cancelTime;
//            $transaction->reason = $reason;
//            $transaction->save();
//
//            // Обновляем заказ
//            $order = $transaction->order;
//            if ($order && $order->status !== Order::STATUS_CANCELLED) { // Если не был отменен вручную
//                $order->status = Order::STATUS_PAYMENT_CANCELLED; // Ваш статус "Оплата отменена"
//                $order->save();
//                // TODO: Логика возврата товаров на склад
//            }
//
//            return $this->successResponse($requestId, [
//                'cancel_time' => (int)$transaction->cancel_time,
//                'transaction' => (string)$transaction->id,
//                'state' => (int)$transaction->state,
//            ]);
//        });
//    }
//
//    protected function checkTransaction(array $params, $requestId)
//    {
//        $validator = Validator::make($params, ['id' => 'required|string|max:25']);
//        if ($validator->fails()) {
//            throw new PaymeException('Неверные параметры.', -31050, $validator->errors()->first());
//        }
//        $paymeTransactionId = $params['id'];
//
//        $transaction = PaymeTransaction::where('paycom_transaction_id', $paymeTransactionId)->first();
//
//        if (!$transaction) {
//            throw new PaymeException('Транзакция не найдена.', -31003);
//        }
//
//        return $this->successResponse($requestId, [
//            'create_time'   => (int)($transaction->create_time ?: $transaction->paycom_time), // Если нашего create_time нет, берем от Payme
//            'perform_time'  => (int)($transaction->perform_time ?: 0),
//            'cancel_time'   => (int)($transaction->cancel_time ?: 0),
//            'transaction'   => (string)$transaction->id, // ID нашей транзакции
//            'state'         => (int)$transaction->state,
//            'reason'        => $transaction->state < 0 ? ($transaction->reason ?? null) : null,
//        ]);
//    }
//
//
//    // Вспомогательные методы для ответов
//    protected function successResponse($requestId, array $result)
//    {
//        return response()->json(['result' => $result, 'id' => $requestId]);
//    }
//
//    protected function errorResponse($requestId, int $code, $message, $data = null)
//    {
//        $error = ['code' => $code, 'message' => $message];
//        if ($data !== null) {
//            $error['data'] = $data;
//        }
//        return response()->json(['error' => $error, 'id' => $requestId], 200); // Payme ожидает HTTP 200 даже для ошибок приложения
//    }
}
