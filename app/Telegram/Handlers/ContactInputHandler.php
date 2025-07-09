<?php
namespace App\Telegram\Handlers;


use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class ContactInputHandler extends BaseHandler
{
    public function handle(): void
    {
        $state = $this->getState();
        if ($state === 'feedback_awaiting_contact') {
            $this->handleFeedbackContact();
            return;
        }

        if ($state === 'checkout_awaiting_phone') {
            $phone = $this->update['message']['contact']['phone_number'];
            if (!str_starts_with($phone, '+')) {
                $phone = '+' . $phone;
            }

            $context = $this->getContext();
            $context['phone_number'] = $phone;
            $this->setContext($context);

            (new CheckoutHandler($this->update))->askForNewAddressText();
        }
    }

    private function handleFeedbackContact(): void
    {
        $context = $this->getContext();
        $name = $context['feedback_name'] ?? 'Не указано';
        $phone = $this->update['message']['contact']['phone_number'];

        $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID');
        if (!$adminChatId) {
            Log::error('TELEGRAM_ADMIN_CHAT_ID не установлен в .env');
            return;
        }

        $notificationText = "🔔 *Заявка на обратный звонок!*\n\n";
        $notificationText .= "👤 *Имя:* {$name}\n";
        $notificationText .= "📞 *Телефон:* `+{$phone}`\n\n";
        $notificationText .= "*От пользователя:* {$this->user->name} (`{$this->user->telegram_id}`)";

        Telegram::sendMessage([
            'chat_id' => $adminChatId,
            'text' => $notificationText,
            'parse_mode' => 'Markdown'
        ]);

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => "Спасибо! Ваша заявка принята. Мы скоро с вами свяжемся.",
            'reply_markup' => ['remove_keyboard' => true]
        ]);

        $this->setState(null);
        $this->setContext([]);
    }
}
