<?php
namespace App\Telegram\Handlers;

use App\Models\Category;
use App\Models\User;
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
                $this->showCategories();
                break;
            case 'ℹ️ О нас':
                $this->handleAbout(); // Вызываем метод для "О нас"
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
        User::updateOrCreate(['telegram_id' => $this->chatId], ['name' => $this->update['message']['from']['first_name'] ?? 'Пользователь']);

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => 'Добро пожаловать в наш магазин! 👋',
            'reply_markup' => MainMenuKeyboard::build(),
        ]);
    }

    public function showCategories(): void
    {
        // ИСПРАВЛЕНИЕ: Берем только активные категории
        $categories = Category::whereNull('parent_id')->where('is_active', true)->get();

        if ($categories->isEmpty()) {
            Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => 'К сожалению, сейчас нет доступных категорий.']);
            return;
        }

        $keyboard = Keyboard::make()->inline();
        foreach ($categories as $category) {
            $keyboard->row([
                Keyboard::inlineButton(['text' => $category->name, 'callback_data' => 'category_' . $category->id]),
            ]);
        }

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
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
        $user = \App\Models\User::where('telegram_id', $this->chatId)->first();
        if (!$user) return; // Если пользователя нет, ничего не делаем

        $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID');
        if (!$adminChatId) return;

        $notificationText = "💬 *Новое сообщение от клиента!*\n\n";
        $notificationText .= "*От:* {$user->name} (`{$user->telegram_id}`)\n";
        $notificationText .= "*Сообщение:* {$this->text}";

        Telegram::sendMessage(['chat_id' => $adminChatId, 'text' => $notificationText, 'parse_mode' => 'Markdown']);
        Telegram::sendMessage(['chat_id' => $this->chatId, 'text' => "Спасибо! Ваше сообщение принято."]);
    }

}
