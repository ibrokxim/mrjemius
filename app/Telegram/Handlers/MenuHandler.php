<?php
namespace App\Telegram\Handlers;

use App\Models\User;
use App\Models\Category;
use App\Services\CartService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Telegram\Keyboards\MainMenuKeyboard;

class MenuHandler extends BaseHandler
{
    public function handle(): void
    {
        switch ($this->text) {
            case '/start':
                $this->handleStart();
                break;

            case '/catalog':
            case '🛍 Каталог':
                self::showCategories($this->chatId);
                break;

            case '/cart':
            case '🛒 Корзина':
                $this->showCart();
                break;

            case '/myorders':
            case '👤 Мои заказы':
                $this->showMyOrders(1);
                break;

            case '/support':
            case '📞 Обратная связь':
                $this->handleFeedback(); // Вызываем метод для "Обратной связи"
                break;

            default:
                break;

        }
    }

    protected function handleStart(): void
    {
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

    protected function handleFeedback(): void
    {
        $text = "📞 *Связаться с нами:*\n\n";
        $text .= "Вы можете позвонить нам напрямую по номеру:\n`+998 77 132 77 00`\n\n";
        $text .= "Либо напишите нашему менеджеру напрямую в Telegram для быстрой консультации:\n";
        $text .= "➡️ **[@mrdjemiuszerouz](https://t.me/mrdjemiuszerouz)**"; // Создаем кликабельную ссылку

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => $text,
            'parse_mode' => 'Markdown', // Указываем Markdown для обработки ссылок и форматирования
            'disable_web_page_preview' => true, // Отключаем превью ссылки для более чистого вида
        ]);
    }

    public function showCart(bool $isEdit = false, ?int $messageId = null): void
    {
        auth()->login($this->user);
        $summary = (new CartService())->getSummary();
        auth()->logout();

        if ($summary['count'] === 0) {
            $text = 'Ваша корзина пуста.';
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

    public function showMyOrders(int $page = 1): void
    {
        $perPage = 5;

        $orders = $this->user->orders()
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        if ($orders->isEmpty() && $page === 1) {
            Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => 'У вас пока нет заказов.']);
            return;
        }

        $text = "👤 *Ваши заказы:*\n\nВыберите заказ, чтобы посмотреть детали.";
        $keyboard = Keyboard::make()->inline();

        foreach ($orders as $order) {
            $statusIcon = $this->getStatusIcon($order->status);
            $date = $order->created_at->format('d.m.Y');
            $buttonText = "{$statusIcon} Заказ №{$order->order_number} от {$date}";
            $keyboard->row([
                Keyboard::inlineButton(['text' => $buttonText, 'callback_data' => 'order_details_' . $order->id])
            ]);
        }

        // --- БЛОК ПАГИНАЦИИ ---
        $paginationRow = [];
        if ($orders->previousPageUrl()) {
            $paginationRow[] = Keyboard::inlineButton(['text' => '◀️', 'callback_data' => 'orders_page_' . ($page - 1)]);
        }
        if ($orders->hasMorePages()) {
            $paginationRow[] = Keyboard::inlineButton(['text' => '▶️', 'callback_data' => 'orders_page_' . ($page + 1)]);
        }
        if (!empty($paginationRow)) {
            $keyboard->row($paginationRow);
        }

        // Если это не первая страница (т.е. мы пришли по кнопке пагинации), то редактируем
        if ($page > 1 || $this->callbackData) { // $this->callbackData - признак того, что мы пришли по кнопке
            Telegram::editMessageText([
                'chat_id' => $this->chatId,
                'message_id' => $this->messageId,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_markup' => $keyboard,
            ]);
        } else {
            Telegram::sendMessage([
                'chat_id' => $this->chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_markup' => $keyboard,
            ]);
        }
    }

    public function getStatusIcon(string $status): string
    {
        return match ($status) {
            'pending' => '🕒',
            'processing' => '⚙️',
            'shipped' => '🚚',
            'delivered' => '✅',
            'cancelled' => '❌',
            default => '📄',
        };
    }

}
