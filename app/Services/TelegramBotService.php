<?php

namespace App\Services;

use App\Telegram\Handlers\MenuHandler;
use App\Telegram\Handlers\CallbackQueryHandler;

class TelegramBotService
{
    public function handleUpdate(array $updateData): void
    {
        // Если это нажатие на inline-кнопку
        if (isset($updateData['callback_query'])) {
            (new CallbackQueryHandler($updateData))->handle();
        }
        // Если это текстовое сообщение
        elseif (isset($updateData['message']['text'])) {
            (new MenuHandler($updateData))->handle();
        }
    }
}
