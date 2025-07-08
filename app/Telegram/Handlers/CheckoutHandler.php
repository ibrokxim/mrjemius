<?php
// Файл: app/Telegram/Handlers/CheckoutHandler.php

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
    /**
     * Главный маршрутизатор для шагов оформления заказа.
     */
    public function handle(): void
    {
        $parts = explode('_', $this->callbackData);
        $step = $parts[1] ?? null;

        switch ($step) {
            case 'start': $this->start(); break;
            case 'address': $this->handleAddressSelection($parts[2] ?? null); break;
            case 'payment': $this->handlePaymentMethod($parts[2] ?? null); break;
            case 'confirm': $this->createOrder(); break;
            case 'cancel': $this->cancelCheckout(); break;
        }
    }

    /**
     * ШАГ 1: Начало оформления. Сразу запрашиваем адрес.
     */
    public function start(): void
    {
        // Сразу устанавливаем, что доставка курьером, и чистим контекст
        $this->setContext(['delivery_method' => 'delivery']);

        // Удаляем сообщение с корзиной
        try {
            Telegram::deleteMessage(['chat_id' => $this->chatId, 'message_id' => $this->messageId]);
        } catch (\Exception $e) {}

        $this->askForAddress();
    }

    /**
     * ШАГ 2: Запрос адреса.
     */
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

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => 'Выберите сохраненный адрес или укажите новый:',
            'reply_markup' => $keyboard
        ]);
    }

    /**
     * Обработка выбора адреса (когда нажата кнопка).
     */
    public function handleAddressSelection($addressId): void
    {
        try { Telegram::deleteMessage(['chat_id' => $this->chatId, 'message_id' => $this->messageId]); } catch (\Exception $e) {}

        if ($addressId === 'new') {
            $this->setState('checkout_awaiting_phone'); // Сразу просим телефон
            Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => 'Напишите ваш контактный номер телефона:']);
        } else {
            $context = $this->getContext();
            $context['address_id'] = $addressId;
            $this->setContext($context);
            $this->askForPaymentMethod(); // Если адрес выбран, сразу переходим к оплате
        }
    }

    /**
     * ШАГ 3: Запрос телефона (вызывается из обработчика текстового ввода).
     */
    public function askForPhone(): void
    {
        $this->setState('checkout_awaiting_address_text');
        Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => "Отлично! Теперь напишите ваш адрес в формате:\n`Город, Улица, Дом, Квартира`"]);
    }

    /**
     * ШАГ 4: Запрос способа оплаты.
     */
    public function askForPaymentMethod(): void
    {
        $this->setState('checkout_awaiting_payment');

        $keyboard = Keyboard::make()->inline()->row([
            Keyboard::inlineButton(['text' => '💵 Наличными', 'callback_data' => 'checkout_payment_cash']),
            Keyboard::inlineButton(['text' => '💳 Картой онлайн (Payme)', 'callback_data' => 'checkout_payment_card_online']),
        ]);
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
        $this->setState(null); // Сбрасываем состояние, чтобы пользователь не мог ввести что-то еще
        $context = $this->getContext();

        // ... (Здесь можно собрать красивое сообщение со всеми данными заказа из контекста) ...
        $text = "Заказ почти оформлен! Пожалуйста, проверьте данные и подтвердите.";

        $keyboard = Keyboard::make()->inline()->row([
            Keyboard::inlineButton(['text' => '✅ Все верно, оформить', 'callback_data' => 'checkout_confirm']),
            Keyboard::inlineButton(['text' => '❌ Отмена', 'callback_data' => 'checkout_cancel']),
        ]);
        Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => $text, 'reply_markup' => $keyboard]);
    }

    /**
     * ШАГ 6: Создание заказа в базе данных.
     */
    public function createOrder(): void
    {
        $context = $this->getContext();
        $user = $this->user;

        auth()->login($user);
        $cartService = new CartService();
        $cartSummary = $cartService->getSummary();
        $cartItems = $cartService->getItems();
        auth()->logout();

        if ($cartItems->isEmpty()) {
            Telegram::editMessageText(['chat_id' => $this->chatId, 'message_id' => $this->messageId, 'text' => 'Ваша корзина пуста.']);
            return;
        }

        $order = null;
        DB::beginTransaction();
        try {
            $shippingAddressId = $context['address_id'] ?? null;
            // Если пользователь вводил новый адрес
            if (isset($context['new_address_text']) && isset($context['phone_number'])) {
                // Предполагаем, что в `new_address_text` формат "Город, Адрес"
                [$city, $addressLine1] = array_map('trim', explode(',', $context['new_address_text'], 2));

                $newAddress = Address::create([
                    'user_id' => $user->id, 'type' => 'shipping',
                    'full_name' => $user->name, // Берем основное имя
                    'phone_number' => $context['phone_number'],
                    'address_line_1' => $addressLine1, 'city' => $city,
                    'country_code' => 'UZ',
                ]);
                $shippingAddressId = $newAddress->id;
            }

            $order = Order::create([
                'order_number' => 'ORD-BOT-' . time(),
                'user_id' => $user->id,
                'shipping_address_id' => $shippingAddressId,
                'status' => 'pending', 'payment_status' => 'pending',
                'subtotal_amount' => $cartSummary['subtotal'],
                'shipping_amount' => $cartSummary['shipping'] ?? 0,
                'total_amount' => $cartSummary['total'],
                'shipping_method' => 'delivery', // Теперь всегда доставка
                'payment_method' => $context['payment_method'],
                'source' => 'telegram_bot', // Указываем источник
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

            // Очищаем корзину и контекст
            auth()->login($user);
            $cartService->clear();
            auth()->logout();
            $this->setContext([]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при создании заказа из бота: ' . $e->getMessage());
            Telegram::editMessageText(['chat_id' => $this->chatId, 'message_id' => $this->messageId, 'text' => 'Произошла ошибка при создании заказа. Попробуйте снова.']);
            return;
        }

        // Финальные действия
        Telegram::editMessageText(['chat_id' => $this->chatId, 'message_id' => $this->messageId, 'text' => "✅ Ваш заказ №{$order->order_number} успешно создан!"]);

        if ($order->payment_method === 'cash') {
            $order->update(['status' => 'processing']);
            (new NotificationService())->sendOrderNotifications($order);
        } else {
            // Здесь должна быть логика отправки ссылки на Payme
            // ...
        }
    }

    public function cancelCheckout(): void
    {
        $this->setState(null);
        $this->setContext([]);
        try {
            Telegram::editMessageText(['chat_id' => $this->chatId, 'message_id' => $this->messageId, 'text' => 'Оформление заказа отменено.']);
        } catch (\Exception $e) {}
    }
}
