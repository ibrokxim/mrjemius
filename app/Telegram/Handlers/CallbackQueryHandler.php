<?php

namespace App\Telegram\Handlers;

use App\Models\Product;
use Exception;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class CallbackQueryHandler extends BaseHandler
{
    public function handle(): void
    {
        $parts = explode('_', $this->callbackData);
        $action = $parts[0] ?? null;

        switch ($action) {
            case 'product':
                if (($parts[1] ?? null) === 'show') {
                    $productId = (int)($parts[2] ?? 0);
                    // Вызываем статический метод для показа товара
                    CatalogHandler::showProduct($this->chatId, $productId, $this->messageId);
                }
                break;
            case 'category':
            case 'products':
            case 'addtocart':
                (new CatalogHandler($this->update))->handle();
                break;

            case 'back':
                if (($parts[1] ?? null) === 'to' && ($parts[2] ?? null) === 'categories') {
                    (new MenuHandler($this->update))::showCategories($this->chatId);
                    Telegram::deleteMessage(['chat_id' => $this->chatId, 'message_id' => $this->messageId]);
                }
                if (($parts[1] ?? null) === 'to' && ($parts[2] ?? null) === 'orders' && ($parts[3] ?? null) === 'list') {
                    (new MenuHandler($this->update))->showMyOrders(1);
                }
                break;

            case 'cart':
                (new CartHandler($this->update))->handle();
                break;
            case 'order':
                if (($parts[1] ?? null) === 'details') {
                    $this->showOrderDetails($parts[2] ?? null);
                }
                break;
            case 'orders':
                if (($parts[1] ?? null) === 'page') {
                    $page = (int)($parts[2] ?? 1);
                    (new MenuHandler($this->update))->showMyOrders($page);
                }
                break;

            case 'checkout':
                (new CheckoutHandler($this->update))->handle();
                break;

            case 'noop':
                Telegram::answerCallbackQuery(['callback_query_id' => $this->update['callback_query']['id']]);
                break;
        }
    }

    protected function showOrderDetails(?int $orderId): void
    {
        if (!$orderId) return;
        $order = $this->user->orders()->with('items.product')->find($orderId);

        if (!$order) {
            Telegram::answerCallbackQuery(['callback_query_id' => $this->update['callback_query']['id'], 'text' => 'Заказ не найден.', 'show_alert' => true]);
            return;
        }

        $statusIcon = (new MenuHandler($this->update))->getStatusIcon($order->status);
        $text = "📄 *Детали заказа №{$order->order_number}*\n\n";
        $text .= "*Статус:* {$statusIcon} " . ucfirst($order->status) . "\n";
        $text .= "*Дата:* " . $order->created_at->format('d.m.Y H:i') . "\n";
        $text .= "*Сумма:* " . number_format($order->total_amount, 0, '.', ' ') . " сум\n\n";
        $text .= "*Состав заказа:*\n";
        foreach ($order->items as $item) {
            $text .= "\\- {$item->product_name} (x{$item->quantity})\n";
        }

        $keyboard = Keyboard::make()->inline()->row([
            Keyboard::inlineButton(['text' => '🔙 Назад к списку заказов', 'callback_data' => 'back_to_orders_list'])
        ]);

        Telegram::editMessageText([
            'chat_id' => $this->chatId, 'message_id' => $this->messageId,
            'text' => $text, 'parse_mode' => 'Markdown', 'reply_markup' => $keyboard,
        ]);
    }
}
