<?php
namespace App\Telegram\Handlers;

use App\Services\CartService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class CartHandler extends BaseHandler
{
    public function handle(): void
    {
        $parts = explode('_', $this->callbackData);
        $action = $parts[1] ?? null;
        $cartItemId = $parts[2] ?? null;

        switch ($action) {
            case 'increase': $this->updateQuantity($cartItemId, 1); break;
            case 'decrease': $this->updateQuantity($cartItemId, -1); break;
            case 'remove': $this->removeItem($cartItemId); break;
            case 'clear': $this->clearCart(); break;
        }
    }

    private function updateQuantity(?int $id, int $change): void
    {
        if (!$id) return;
        auth()->login($this->user);
        $cartService = new CartService();
        $item = $cartService->findItem($id);
        if ($item) {
            $newQty = $item->quantity + $change;
            if ($newQty > 0) {
                // Проверяем остатки на складе
                if ($newQty <= $item->product->stock_quantity) {
                    $cartService->update($id, $newQty);
                } else {
                    Telegram::answerCallbackQuery(['callback_query_id' => $this->update['callback_query']['id'], 'text' => 'Больше нет в наличии!', 'show_alert' => true]);
                }
            } else {
                $cartService->remove($id);
            }
        }
        auth()->logout();
        $this->editCartMessage();
    }

    private function removeItem(?int $id): void
    {
        if (!$id) return;
        auth()->login($this->user);
        (new CartService())->remove($id);
        auth()->logout();
        $this->editCartMessage();
    }

    private function clearCart(): void
    {
        auth()->login($this->user);
        (new CartService())->clear();
        auth()->logout();
        $this->editCartMessage(true); // Передаем флаг, что корзина точно пуста
    }

    private function editCartMessage(bool $isCleared = false): void
    {
        auth()->login($this->user);
        $summary = (new CartService())->getSummary();
        auth()->logout();

        if ($summary['count'] === 0) {
            $text = $isCleared ? '✅ Корзина успешно очищена.' : 'Ваша корзина пуста.';
            Telegram::editMessageText(['chat_id' => $this->chatId, 'message_id' => $this->messageId, 'text' => $text, 'reply_markup' => null]);
            return;
        }

        $text = "🛒 *Ваша корзина:*\n\n";
        $keyboard = Keyboard::make()->inline();
        foreach ($summary['items'] as $item) {
            $productName = $item->product->getTranslation('name', 'ru');
            $text .= "▪️ *{$productName}*\n";
            $text .= "    `{$item->quantity} шт. x " . number_format($item->product->price, 0, '.', ' ') . " сум = " . number_format($item->product->price * $item->quantity, 0, '.', ' ') . " сум`\n";
            $keyboard->row([
                Keyboard::inlineButton(['text' => '➖', 'callback_data' => 'cart_decrease_' . $item->id]),
                Keyboard::inlineButton(['text' => "{$item->quantity} шт.", 'callback_data' => 'noop']),
                Keyboard::inlineButton(['text' => '➕', 'callback_data' => 'cart_increase_' . $item->id]),
                Keyboard::inlineButton(['text' => '❌', 'callback_data' => 'cart_remove_' . $item->id]),
            ]);
        }

        $text .= "\n\n*Сумма товаров:* " . number_format($summary['subtotal'], 0, '.', ' ') . " сум";
        $text .= "\n*Доставка:* " . ($summary['shipping'] > 0 ? "20 000 сум (оплачивается отдельно)" : "Бесплатно");
        $text .= "\n\n*Итого к онлайн-оплате:* " . number_format($summary['total'], 0, '.', ' ') . " сум";

        $keyboard->row([Keyboard::inlineButton(['text' => '✅ Оформить заказ', 'callback_data' => 'checkout_start'])]);
        $keyboard->row([Keyboard::inlineButton(['text' => '🗑 Очистить корзину', 'callback_data' => 'cart_clear']), Keyboard::inlineButton(['text' => '🛍 Продолжить покупки', 'callback_data' => 'back_to_categories'])]);

        try {
            Telegram::editMessageText(['chat_id' => $this->chatId, 'message_id' => $this->messageId, 'text' => $text, 'parse_mode' => 'Markdown', 'reply_markup' => $keyboard]);
        } catch (\Exception $e) {
            Log::info("Cart edit message error (возможно, текст не изменился): " . $e->getMessage());
        }
    }
}
