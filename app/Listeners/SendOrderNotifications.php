<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Telegram\Bot\Laravel\Facades\Telegram;

class SendOrderNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        \Log::info("Слушатель SendOrderNotifications сработал для заказа №{$event->order->id}.");
        $order = $event->order;
        $user = $order->user;

        // --- 1. Отправка сообщения клиенту ---
        if ($user && $user->telegram_id) { // Проверяем, есть ли у пользователя telegram_chat_id
            $clientMessage = "Здравствуйте, {$user->telegram_username}!\n\n";
            $clientMessage .= "Ваш заказ *№{$order->order_number}* успешно оформлен и принят в обработку.\n\n";
            $clientMessage .= "Сумма заказа: *{$order->total_amount} сум*.\n";
            $clientMessage .= "Мы свяжемся с вами для подтверждения деталей доставки.\n\n";
            $clientMessage .= "Спасибо за покупку!";

            try {
                Telegram::sendMessage([
                    'chat_id' => $user->telegram_id,
                    'text' => $clientMessage,
                    'parse_mode' => 'Markdown' // Используем Markdown для форматирования (*жирный*)
                ]);
            } catch (\Exception $e) {
                \Log::error("Не удалось отправить уведомление клиенту: " . $e->getMessage());
            }
        }

        // --- 2. Отправка сообщения в группу администраторов ---
        $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID');
        if ($adminChatId) {
            $adminMessage = "🎉 *Новый заказ!* №{$order->order_number}\n\n";
            $adminMessage .= "*Клиент:* {$user->name} ({$user->phone_number})\n";
            $adminMessage .= "*Сумма:* {$order->total_amount} сум\n";
            $adminMessage .= "*Состав заказа:*\n";

            foreach ($order->items as $item) {
                $adminMessage .= "- {$item->product_name} (x{$item->quantity})\n";
            }

            // Добавляем ссылку на админ-панель, если она у вас есть
            // $adminUrl = route('admin.orders.show', $order->id);
            // $adminMessage .= "\n[Посмотреть заказ в админке]({$adminUrl})";

            try {
                Telegram::sendMessage([
                    'chat_id' => $adminChatId,
                    'text' => $adminMessage,
                    'parse_mode' => 'Markdown'
                ]);
            } catch (\Exception $e) {
                \Log::error("Не удалось отправить уведомление администратору: " . $e->getMessage());
            }
        }
    }
    }
