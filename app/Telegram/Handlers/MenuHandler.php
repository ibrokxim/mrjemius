<?php
namespace App\Telegram\Handlers;

use App\Models\Category;
use App\Models\User;
use App\Services\CartService;
use App\Telegram\Keyboards\MainMenuKeyboard;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class MenuHandler extends BaseHandler
{
    public function handle(): void
    {
        switch ($this->text) {
            case '/start':
                $this->handleStart();
                break;

            case '🛍 Каталог':
                self::showCategories($this->chatId);
                break;
            case '🛒 Корзина':
                $this->showCart();
                break;

            case '📞 Обратная связь':
                $this->handleFeedback(); // Вызываем метод для "Обратной связи"
                break;

            default:
                // Пересылаем любое другое сообщение менеджеру
                $this->forwardMessageToManager();
                break;

            // Другие кнопки главного меню будут здесь
        }
    }

    protected function handleStart(): void
    {
       // User::updateOrCreate(['telegram_id' => $this->chatId], ['name' => $this->update['message']['from']['first_name'] ?? 'Пользователь']);

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => "Добро пожаловать, {$this->user->name}! 👋",
            'reply_markup' => MainMenuKeyboard::build(),
        ]);
    }

    public static function showCategories(int $chatId): void
    {
        $categories = Category::whereNull('parent_id')->where('is_active', true)->get();

        if ($categories->isEmpty()) {
            Telegram::sendMessage(['chat_id' => $chatId, 'text' => 'К сожалению, сейчас нет доступных категорий.']);
            return;
        }

        $keyboard = Keyboard::make()->inline();
        foreach ($categories as $category) {
            $keyboard->row([
                Keyboard::inlineButton(['text' => $category->name, 'callback_data' => 'category_' . $category->id]),
            ]);
        }

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите категорию:',
            'reply_markup' => $keyboard,
        ]);
    }

    protected function handleAbout(): void
    {
        $text = "<b>Mr. Djemius Zero</b> - это магазин вкусных и полезных продуктов без сахара!\n\n";
        $text .= "Наш сайт: mrdjemiuszero.uz\n";
        $text .= "Телефон для связи: +998 77 132 77 00";

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    protected function handleFeedback(): void
    {
        $text = "📞 *Связаться с нами:*\n\n";
        $text .= "Вы можете позвонить нам напрямую по номеру:\n`+998 77 132 77 00`\n\n";
        $text .= "Либо, если вы хотите, чтобы мы вам перезвонили, просто отправьте в этот чат ваше **имя и номер телефона**.";

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }

    protected function forwardMessageToManager(): void
    {
        $user = User::where('telegram_id', $this->chatId)->first();
        if (!$user) return; // Если пользователя нет, ничего не делаем

        $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID');
        if (!$adminChatId) return;

        $notificationText = "💬 *Новое сообщение от клиента!*\n\n";
        $notificationText .= "*От:* {$user->name} (`{$user->telegram_id}`)\n";
        $notificationText .= "*Сообщение:* {$this->text}";

        Telegram::sendMessage(['chat_id' => $adminChatId, 'text' => $notificationText, 'parse_mode' => 'Markdown']);
        Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => "Спасибо! Ваше сообщение принято."]);
    }

    public function showCart(bool $isEdit = false, ?int $messageId = null): void
    {
        auth()->login($this->user);
        $summary = (new CartService())->getSummary();
        auth()->logout();

        if ($summary['count'] === 0) {
            $text = 'Ваша корзина пуста.';
            // Если это редактирование, меняем текст существующего сообщения
            if ($isEdit && $messageId) {
                Telegram::editMessageText(['chat_id' => $this->chatId, 'message_id' => $messageId, 'text' => $text, 'reply_markup' => null]);
            } else {
                Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => $text]);
            }
            return;
        }

        $text = "🛒 *Ваша корзина:*\n\n";
        $keyboard = Keyboard::make()->inline();

        foreach ($summary['items'] as $item) {
            $productName = $item->product->getTranslation('name', 'ru');
            $text .= "▪️ *{$productName}*\n";
            $text .= "    `" . number_format($item->product->price * $item->quantity, 0, '.', ' ') . " сум`\n";

            // --- КНОПКИ УПРАВЛЕНИЯ ДЛЯ КАЖДОГО ТОВАРА ---
            $keyboard->row([
                Keyboard::inlineButton(['text' => '➖', 'callback_data' => 'cart_decrease_' . $item->id]),
                Keyboard::inlineButton(['text' => "{$item->quantity} шт.", 'callback_data' => 'noop']),
                Keyboard::inlineButton(['text' => '➕', 'callback_data' => 'cart_increase_' . $item->id]),
                Keyboard::inlineButton(['text' => '❌', 'callback_data' => 'cart_remove_' . $item->id]),
            ]);
        }

        $text .= "\n\n*Сумма товаров:* " . number_format($summary['subtotal'], 0, '.', ' ') . " сум";
        $text .= "\n\n*Доставка по городу:*\n";
        if ($summary['shipping'] > 0) {
            $text .= "`" . number_format($summary['baseShippingCost'], 0, '.', ' ') . " сум`\n";
        } else {
            $text .= "`Бесплатно`\n";
        }
        $text .= "_(Сумма в пределах большой кольцевой, оплата курьеру наличными)_\n";
        $text .= "\n\n*Итого к онлайн-оплате:* " . number_format($summary['total'], 0, '.', ' ') . " сум";

        // --- ОБЩИЕ КНОПКИ КОРЗИНЫ ---
        $keyboard->row([Keyboard::inlineButton(['text' => '✅ Оформить заказ', 'callback_data' => 'checkout_start'])]);
        $keyboard->row([Keyboard::inlineButton(['text' => '🗑 Очистить корзину', 'callback_data' => 'cart_clear'])]);
        $keyboard->row([Keyboard::inlineButton(['text' => '🛍 Продолжить покупки', 'callback_data' => 'back_to_categories'])]);

        $params = [
            'chat_id' => $this->chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'reply_markup' => $keyboard,
        ];

        if ($isEdit && $messageId) {
            try {
                Telegram::editMessageText(array_merge($params, ['message_id' => $messageId]));
            } catch (\Exception $e) { /* Игнорируем ошибку, если текст не изменился */ }
        } else {
            Telegram::sendMessage($params);
        }
    }

}
