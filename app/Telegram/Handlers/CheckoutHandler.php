<?php

namespace App\Telegram\Handlers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\TelegramService as NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class CheckoutHandler extends BaseHandler
{

    public function handle(): void
    {
        $parts = explode('_', $this->callbackData);
        $step = $parts[1] ?? null;

        switch ($step) {
            case 'start': $this->start(); break;
            case 'address': $this->handleAddressSelection($parts[2] ?? null); break;
            case 'date': $this->handleDateSelection($parts[2] ?? null); break;
            case 'payment':
                $methodParts = array_slice($parts, 2);
                $method = implode('_', $methodParts);
                $this->handlePaymentMethod($method);
                break;
            case 'confirm': $this->createOrder(); break;
            case 'cancel': $this->cancelCheckout(); break;
        }
    }

    public function start(): void
    {
        $this->setContext(['delivery_method' => 'delivery']);

        try {
            Telegram::deleteMessage(['chat_id' => $this->chatId, 'message_id' => $this->messageId]);
        } catch (\Exception $e) {}

        $this->askForAddress();
    }


    public function askForAddress(): void
    {
        $this->setState('checkout_awaiting_address');

        $addresses = $this->user->addresses()->latest()->take(3)->get();
        $keyboard = Keyboard::make()->inline();

        foreach ($addresses as $address) {
            $keyboard->row([Keyboard::inlineButton([
                'text' => "📍 {$address->address_line_1}, {$address->city}",
                'callback_data' => 'checkout_address_' . $address->id
            ])]);
        }

        $keyboard->row([Keyboard::inlineButton(['text' => '➕ Указать другой адрес', 'callback_data' => 'checkout_address_new'])]);
        $keyboard->row([Keyboard::inlineButton(['text' => '❌ Отменить оформление', 'callback_data' => 'checkout_cancel'])]);

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => 'Выберите сохраненный адрес или укажите новый:',
            'reply_markup' => $keyboard
        ]);
    }

    public function handleAddressSelection($addressId): void
    {
        try { Telegram::deleteMessage(['chat_id' => $this->chatId, 'message_id' => $this->messageId]); } catch (\Exception $e) {}

        if ($addressId === 'new') {
            $this->askForNewAddressContact();
        } else {
            $context = $this->getContext();
            $context['address_id'] = $addressId;
            $this->setContext($context);
            $this->askForDeliveryDate();
        }
    }

    public function askForNewAddressContact(): void
    {
        $this->setState('checkout_awaiting_phone');
        $keyboard = Keyboard::make()
            ->row([Keyboard::button(['text' => '📱 Поделиться номером телефона', 'request_contact' => true])])
            ->setResizeKeyboard(true)->setOneTimeKeyboard(true);

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => "Пожалуйста, нажмите на кнопку ниже, чтобы поделиться вашим контактным номером.\n\nИли просто напишите его в чат.",
            'reply_markup' => $keyboard
        ]);
    }

    public function askForNewAddressText(): void
    {
        $this->setState('checkout_awaiting_address_text');
        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => "Отлично! Теперь напишите ваш адрес в формате:\n`Город, Улица, Дом, Квартира`",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode(['remove_keyboard' => true]),
        ]);
    }

    public function askForDeliveryDate(): void
    {
        $this->setState('checkout_awaiting_date');
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        $today = now();
        $tomorrow = now()->addDay();
        $dayAfter = now()->addDays(2);

        $keyboard = Keyboard::make()->inline()->row([
            Keyboard::inlineButton(['text' => 'Сегодня, ' . $today->isoFormat('D MMM'), 'callback_data' => 'checkout_date_' . $today->format('Y-m-d')]),
            Keyboard::inlineButton(['text' => 'Завтра, ' . $tomorrow->isoFormat('D MMM'), 'callback_data' => 'checkout_date_' . $tomorrow->format('Y-m-d')]),
            Keyboard::inlineButton(['text' => 'Послезавтра, ' . $dayAfter->isoFormat('D MMM'), 'callback_data' => 'checkout_date_' . $dayAfter->format('Y-m-d')]),
        ])->row([Keyboard::inlineButton(['text' => '❌ Отменить оформление', 'callback_data' => 'checkout_cancel'])]);

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => 'Выберите желаемую дату доставки:',
            'reply_markup' => $keyboard
        ]);
    }

    public function handleDateSelection(?string $date): void
    {
        if (!$date) return;
        try { Telegram::deleteMessage(['chat_id' => $this->chatId, 'message_id' => $this->messageId]); } catch (\Exception $e) {}

        $context = $this->getContext();
        $context['delivered_at'] = $date;
        $this->setContext($context);

        $this->askForPaymentMethod(); // <-- Переходим к выбору оплаты
    }


    public function askForPaymentMethod(): void
    {
        $this->setState('checkout_awaiting_payment');

        $keyboard = Keyboard::make()->inline()->row([
            Keyboard::inlineButton(['text' => '💵 Наличными', 'callback_data' => 'checkout_payment_cash']),
            Keyboard::inlineButton(['text' => '💳 Картой онлайн (Payme)', 'callback_data' => 'checkout_payment_card_online']),
        ])->row([Keyboard::inlineButton(['text' => '❌ Отменить оформление', 'callback_data' => 'checkout_cancel'])]);
        Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => 'Выберите способ оплаты:', 'reply_markup' => $keyboard]);
    }

    /**
     * Обработка выбора способа оплаты.
     */
    public function handlePaymentMethod(?string $method): void
    {
        if (!$method) return;
        try { Telegram::deleteMessage(['chat_id' => $this->chatId, 'message_id' => $this->messageId]); } catch (\Exception $e) {}

        $context = $this->getContext();
        $context['payment_method'] = $method;
        $this->setContext($context);

        $this->showConfirmation();
    }

    /**
     * ШАГ 5: Финальное подтверждение.
     */
    public function showConfirmation(): void
    {
        $this->setState(null);
        $context = $this->getContext();
        auth()->login($this->user);
        $cartSummary = (new CartService())->getSummary();
        auth()->logout();
        // Формируем текст с деталями заказа
        $text = "🔎 *Проверьте ваш заказ*\n\n";
        $text .= "*Товары на сумму:* " . number_format($cartSummary['subtotal'], 0, '.', ' ') . " сум\n";
        $text .= "*Доставка:* " . number_format($cartSummary['shipping'], 0, '.', ' ') . " сум\n";
        $text .= "*Итого:* " . number_format($cartSummary['total'], 0, '.', ' ') . " сум\n\n";
        $text .= "➖➖➖\n\n";

        // Детали доставки
        if (isset($context['address_id'])) {
            $address = Address::find($context['address_id']);
            $text .= "*Адрес:* {$address->full_text}\n";
            $text .= "*Телефон:* {$address->phone_number}\n";
        } else {
            $text .= "*Адрес:* {$context['new_address_text']}\n";
            $text .= "*Телефон:* {$context['phone_number']}\n";
        }

        // Способ оплаты
        $paymentMethodText = $context['payment_method'] === 'cash' ? '💵 Наличными' : '💳 Картой онлайн';
        $text .= "*Оплата:* {$paymentMethodText}\n";

        $keyboard = Keyboard::make()->inline()->row([
            Keyboard::inlineButton(['text' => '✅ Все верно, оформить', 'callback_data' => 'checkout_confirm']),
            Keyboard::inlineButton(['text' => '❌ Отмена', 'callback_data' => 'checkout_cancel']),
        ]);
        Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => $text, 'parse_mode' => 'Markdown', 'reply_markup' => $keyboard]);
    }

    /**
     * ШАГ 6: Создание заказа в базе данных.
     */
    public function createOrder(): void
    {
        Log::info('[Checkout] Нажата кнопка "Все верно, оформить". Начинаем процесс создания заказа.');

        $context = $this->getContext();
        $user = $this->user;
        Log::info('[Checkout] Контекст и пользователь получены. User ID: ' . $user->id);

        auth()->login($user);
        $cartService = new CartService();
        $cartSummary = $cartService->getSummary();
        $cartItems = $cartService->getItems();
        auth()->logout();
        Log::info('[Checkout] Корзина получена. Количество товаров: ' . $cartItems->count());

        if ($cartItems->isEmpty()) {
            Log::warning('[Checkout] Корзина пуста. Отправляем сообщение пользователю и прерываемся.');
            // Пытаемся отредактировать, если не получится (сообщение уже удалено) - не страшно.
            try {
                Telegram::editMessageText(['chat_id' => $this->chatId, 'message_id' => $this->messageId, 'text' => 'Ваша корзина пуста.']);
            } catch (\Exception $e) {
                Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => 'Ваша корзина пуста.']);
            }
            return;
        }

        $order = null;
        Log::info('[Checkout] Начинаем транзакцию в базе данных.');
        DB::beginTransaction();
        try {
            $shippingAddressId = $context['address_id'] ?? null;
            Log::info('[Checkout] ID сохраненного адреса: ' . ($shippingAddressId ?? 'не указан'));

            if (isset($context['new_address_text']) && isset($context['phone_number'])) {
                Log::info('[Checkout] Обнаружен новый адрес. Создаем его.');
                $addressParts = explode(',', $context['new_address_text'], 2);
                $city = trim($addressParts[0]);
                $addressLine1 = trim($addressParts[1] ?? $city);

                $newAddress = Address::create([
                    'user_id' => $user->id, 'type' => 'shipping', 'full_name' => $user->name,
                    'phone_number' => $context['phone_number'], 'address_line_1' => $addressLine1,
                    'city' => $city, 'country_code' => 'UZ', 'is_default' => false, 'postal_code' => '000000',
                ]);
                $shippingAddressId = $newAddress->id;
            }

            Log::info('[Checkout] Создаем запись заказа (Order).');
            $order = Order::create([
                'order_number' => 'ORD-BOT-' . time() . '-' . $user->id,
                'user_id' => $user->id,
                'shipping_address_id' => $shippingAddressId,
                'status' => 'pending', 'payment_status' => 'pending',
                'subtotal_amount' => $cartSummary['subtotal'],
                'shipping_amount' => $cartSummary['shipping'] ?? 0,
                'total_amount' => $cartSummary['total'],
                'shipping_method' => 'delivery',
                'payment_method' => $context['payment_method'],
                'source' => 'telegram_bot',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id, 'product_id' => $item->product_id,
                    'product_name' => $item->product->getTranslation('name', 'ru'),
                    'quantity' => $item->quantity, 'price_at_purchase' => $item->product->price,
                    'total_price' => $item->product->price * $item->quantity,
                ]);
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            DB::commit();
            Log::info('[Checkout] Транзакция успешно закоммичена.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[Checkout] КРИТИЧЕСКАЯ ОШИБКА при создании заказа: ' . $e->getMessage(), [
                'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTraceAsString()
            ]);
            Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => 'Произошла ошибка при создании заказа. Пожалуйста, обратитесь в поддержку.']);
            return;
        }

        Log::info('[Checkout] Очищаем корзину и контекст пользователя.');
        auth()->login($user);
        $cartService->clear();
        auth()->logout();
        $this->setState(null);
        $this->setContext([]);

        if ($order->payment_method === 'card_online') {
            Log::info("[Checkout] Способ оплаты - карта. Формируем ссылку на Web App для заказа #{$order->id}");

            $params_for_webapp = [
                'order_id' => $order->id,
                'amount' => $order->subtotal_amount * 100,
                'user_id' => $order->user_id,
            ];

            $webAppUrl = route('telegram.payment.show', $params_for_webapp);

            Log::info("[Checkout] Ссылка на Web App: " . $webAppUrl);

            // 3. Создаем кнопку, которая открывает Web App
            $keyboard = \Telegram\Bot\Keyboard\Keyboard::make()->inline()->row([
                \Telegram\Bot\Keyboard\Keyboard::inlineButton([
                    'text' => '💳 Перейти к оплате',
                    'web_app' => ['url' => $webAppUrl]
                ])
            ]);

            try {
                Telegram::deleteMessage(['chat_id' => $this->chatId, 'message_id' => $this->messageId]);
            } catch (\Exception $e) {}

            Telegram::sendMessage([
                'chat_id' => $this->chatId,
                'text' => "✅ *Ваш заказ №{$order->order_number} успешно создан!* \n\nНажмите на кнопку ниже, чтобы перейти к оплате.",
                'parse_mode' => 'Markdown',
                'reply_markup' => $keyboard,
            ]);
    } else {
            $order->update(['status' => 'processing']);
            (new NotificationService())->sendOrderNotifications($order);
            Telegram::sendMessage([
                'chat_id' => $this->chatId,
                'text' => "✅ *Ваш заказ №{$order->order_number} принят в обработку!* \n\nНаш менеджер скоро с вами свяжется.",
                'parse_mode' => 'Markdown'
            ]);
            Log::info("[Checkout] Уведомления для заказа с оплатой наличными отправлены. Процесс завершен.");
        }
    }


    public function cancelCheckout(): void
    {
        $this->setState(null);
        $this->setContext([]);
        try {
            Telegram::deleteMessage(['chat_id' => $this->chatId, 'message_id' => $this->messageId]);
        } catch (\Exception $e) {}

        Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => 'Оформление заказа отменено.']);
        (new MenuHandler($this->update))->showCart();
    }
}
