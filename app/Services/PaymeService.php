<?php

namespace App\Services;

class PaymeService
{
    protected string $merchantId;
    protected string $apiKey; // Ключ для API (НЕ кассовый ключ)
    protected string $checkoutUrl;

    public function __construct()
    {
        $this->merchantId = config('payme.merchant_id');
        $this->apiKey = config('payme.api_key'); // Убедитесь, что это ключ для X-Auth
        $this->checkoutUrl = config('payme.checkout_url');

        if (empty($this->merchantId) || empty($this->apiKey) || empty($this->checkoutUrl)) {
            Log::critical('Payme Service: Configuration missing (merchant_id, api_key, or checkout_url).');
            // Выбросить исключение или обработать
        }
    }

    protected function getAuthHeaders(): array
    {
        // Аутентификация запросов с ключом: X-Auth: <MERCHANT_ID>:<KEY>
        // KEY - это ключ для доступа к API, предоставляемый Payme.
        return [
            'X-Auth' => $this->merchantId . ':' . $this->apiKey,
        ];
    }

    protected function sendRequest(string $method, array $params)
    {
        $requestId = (string) Str::uuid(); // Генерируем уникальный ID для запроса
        $payload = [
            'id' => $requestId,
            'method' => $method,
            'params' => $params,
        ];

        Log::info('Sending Payme API Request:', ['url' => $this->checkoutUrl, 'payload' => $payload]);

        $response = Http::withHeaders($this->getAuthHeaders())
            ->timeout(30) // Таймаут запроса
            ->post($this->checkoutUrl, $payload);

        Log::info('Received Payme API Response:', [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body()
        ]);

        return $response;
    }

    /**
     * Создание чека/инвойса для оплаты.
     *
     * @param int $orderId Ваш внутренний ID заказа
     * @param int $amount Сумма в ТИИНАХ
     * @param array $accountDetails ['order_id' => (string)$orderId, 'description' => 'Оплата заказа #...']
     * @param array $items Опционально, для фискализации
     * @param bool $sendToFiscalModule Отправлять ли сразу на фискализацию
     * @return \Illuminate\Http\Client\Response
     */
    public function createReceipt(int $orderId, int $amount, array $accountDetails, array $items = [], bool $sendToFiscalModule = true)
    {
        $params = [
            'amount' => $amount,
            'account' => $accountDetails,
        ];

        if (!empty($items) && $sendToFiscalModule) {
            $params['detail'] = [
                'receipt_type' => 0, // 0 - приход
                'items' => $items,   // Массив товаров
            ];
            // Документация Payme не очень ясна насчет этого параметра,
            // но если 'detail' присутствует, то фискализация должна происходить.
            // Возможно, есть отдельный параметр для управления этим.
        }

        return $this->sendRequest('receipts.create', $params);
    }

    /**
     * Проверка статуса чека
     * @param string $paymeReceiptId ID чека, полученный от Payme
     */
    public function checkReceiptStatus(string $paymeReceiptId)
    {
        return $this->sendRequest('receipts.check', ['id' => $paymeReceiptId]);
    }

    /**
     * Отмена чека (если он еще не оплачен)
     */
    public function cancelReceipt(string $paymeReceiptId, int $reason = 1)
    {
        return $this->sendRequest('receipts.cancel', [
            'id' => $paymeReceiptId,
            'reason' => $reason
        ]);
    }

    /**
     * Отправка фискальных данных для существующего чека (если не было сделано при создании)
     * @param string $paymeReceiptId
     * @param array $items
     * @param int $receiptType
     */
    public function sendReceiptToFiscalModule(string $paymeReceiptId, array $items, int $receiptType = 0)
    {
        $params = [
            'id' => $paymeReceiptId,
            'detail' => [
                'receipt_type' => $receiptType,
                'items' => $items,
            ],
        ];
        return $this->sendRequest('receipts.send', $params);
    }

    // Методы для cards.create, cards.get_verify_code, cards.verify, cards.check, cards.remove
    // можно добавить по аналогии, если они вам нужны.
    // Например, cards.create:
    /*
    public function createCardToken(string $cardNumber, string $expireDate, bool $save = false)
    {
        return $this->sendRequest('cards.create', [
            'card' => ['pan' => $cardNumber, 'expire' => $expireDate], // expire в формате "MMYY"
            'save' => $save,
        ]);
    }
    */
}
