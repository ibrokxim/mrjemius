<?php
namespace App\Telegram\Keyboards;

use Telegram\Bot\Keyboard\Keyboard;

class FeedbackKeyboard
{
    public static function build(): Keyboard
    {
        return Keyboard::make()
            ->inline() // <-- ВАЖНО: Это будут inline-кнопки под сообщением
            ->row([
                Keyboard::inlineButton(['text' => '✍️ Задать вопрос менеджеру', 'callback_data' => 'ask_question']),
            ])
            ->row([
                Keyboard::inlineButton(['text' => '☎️ Заказать обратный звонок', 'callback_data' => 'request_call']),
            ]);
    }


}
