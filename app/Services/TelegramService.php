<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    public function sendOrderNotifications(Order $order): void
    {
//        $this->notifyAdmin($order);
        $this->notifyClient($order);
    }

    /**
     * Уведомляет администраторов о новом заказе.
     */
    protected function notifyAdmin(Order $order): void
    {
        $adminChatId = -4857413796; // верни сюда env() если тест прошёл

        if (!$adminChatId) {
            Log::warning("notifyAdmin: TELEGRAM_ADMIN_CHAT_ID не установлен.");
            return;
        }

        $user = $order->user;
        $address = $order->shippingAddress;

        // Экранируем только переменные
        $orderNumber = $this->escapeMarkdown($order->order_number);
        $userName = $this->escapeMarkdown($user->name ?? 'Не указано');
        $userPhone = $address->phone_number ?? 'Не указан'; // Не требует экранирования в код-блоке
        $addressString = $this->escapeMarkdown($address ? "{$address->city}, {$address->address_line_1}" : 'Не указан');
        $customerNotes = $this->escapeMarkdown($order->customer_notes ?? '');

        // Заголовок: вручную экранируем восклицательный знак
        $adminMessage = "🎉 *Новый заказ\\!* №{$orderNumber}\n\n";

        $adminMessage .= "*Клиент:*\n";
        $adminMessage .= "  • *Имя:* {$userName}\n";
        $adminMessage .= "  • *Телефон:* `{$userPhone}`\n\n";

        $adminMessage .= "*Детали заказа:*\n";
        $adminMessage .= "  • *Адрес:* {$addressString}\n\n";

        $adminMessage .= "*Состав заказа:*\n";
        foreach ($order->items as $item) {
            $productName = $this->escapeMarkdown($item->product_name);
            $adminMessage .= "• {$productName} \\(x{$item->quantity}\\)\n";
        }
        $adminMessage .= "\n";

        if (!empty($customerNotes)) {
            $adminMessage .= "*Комментарий клиента:*\n";
            $adminMessage .= "\\_{$customerNotes}\\_\n\n";
        }

        try {
            Telegram::sendMessage([
                'chat_id' => $adminChatId,
                'text' => $adminMessage,
                'parse_mode' => 'MarkdownV2'
            ]);
            Log::info("Уведомление админу для заказа №{$order->order_number} отправлено.");
        } catch (\Exception $e) {
            Log::error("Ошибка отправки админу для заказа №{$order->order_number}: " . $e->getMessage());
        }
    }


    /**
     * Уведомляет клиента об успешном оформлении заказа.
     */
    protected function notifyClient(Order $order): void
    {
        $user = $order->user;
        if (!$user || !$user->telegram_id) {
            Log::warning("Не удалось отправить уведомление клиенту для заказа №{$order->order_number}: отсутствует telegram_chat_id.");
            return;
        }
        $paymentMethodText = '';
        if ($order->payment_method === 'cash') {
            $paymentMethodText = 'Наличными при получении';
        } elseif ($order->payment_method === 'card_online') {
            $paymentMethodText = 'Картой онлайн (оплачено)'; // Или любой другой подходящий текст
        } else {
            // На случай, если появятся другие методы
            $paymentMethodText = $order->payment_method;
        }
        $clientMessage = "Здравствуйте, {$user->name}!\n\n";
        $clientMessage .= "Ваш заказ **№{$order->order_number}** успешно оформлен.\n\n";
        $clientMessage .= "Сумма заказа: **" . number_format($order->total_amount, 0, '.', ' ') . " сум**.\n";
        $clientMessage .= "Тип оплаты: {$paymentMethodText}\n\n";
        $clientMessage .= "Мы скоро свяжемся с вами для подтверждения.\n\n";
        $clientMessage .= "Если у вас возникнут какие-либо вопросы, пожалуйста, звоните по номеру: +998 77 132 77 00\n\n";
        $clientMessage .= "Спасибо за покупку!";

        try {
            Telegram::sendMessage([
                'chat_id' => $user->telegram_id,
                'text' => $clientMessage,
                'parse_mode' => 'Markdown'
            ]);
        } catch (\Exception $e) {
            Log::error("Ошибка отправки уведомления клиенту для заказа №{$order->order_number}: " . $e->getMessage());
        }
    }

    private function formatPaymentMethod(string $method): string
    {
        return match ($method) {
            'cash' => 'Наличными при получении',
            'card_online' => 'Картой онлайн (Payme/Uzcard)',
            default => 'Не указан',
        };
    }

    private function escapeMarkdown(?string $text): string
    {
        if ($text === null) {
            return '';
        }
        // Список символов, которые нужно экранировать для MarkdownV2
        $escapeChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];

        return str_replace($escapeChars, array_map(fn($char) => '\\' . $char, $escapeChars), $text);
    }

    public function sendFeedbackNotification(string $name, string $phone): void
    {
        $adminChatId = -4857413796 ;
        if (!$adminChatId) {
            Log::warning("Не удалось отправить заявку: TELEGRAM_ADMIN_CHAT_ID не установлен.");
            return;
        }

        // Экранируем данные от пользователя
        $safeName = $this->escapeMarkdown($name);
        $safePhone = $this->escapeMarkdown($phone);

        // Формируем сообщение
        $message = "📞 **Заявка на обратный звонок\\!**\n\n";
        $message .= "👤 **Имя:** {$safeName}\n";
        $message .= "📱 **Телефон:** `{$safePhone}`"; // В `code` блоке для удобного копирования

        try {
            Telegram::sendMessage([
                'chat_id' => $adminChatId,
                'text' => $message,
                'parse_mode' => 'MarkdownV2'
            ]);
            Log::info("Уведомление об обратной связи от {$safeName} успешно отправлено.");
        } catch (\Exception $e) {
            Log::error("Ошибка отправки уведомления об обратной связи: " . $e->getMessage());
            // Перебрасываем исключение, чтобы контроллер знал об ошибке
            throw $e;
        }
    }
}
