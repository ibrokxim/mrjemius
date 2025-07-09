<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;
use App\Telegram\Handlers\TextInputHandler;
use App\Telegram\Handlers\ContactInputHandler;
use App\Telegram\Handlers\CallbackQueryHandler;

class TelegramBotService
{
    public function handleUpdate(array $updateData): void
    {
        try {
            if (isset($updateData['callback_query'])) {
                (new CallbackQueryHandler($updateData))->handle();
            }
            elseif (isset($updateData['message']['contact'])) {
                (new ContactInputHandler($updateData))->handle();
            }
            elseif (isset($updateData['message']['text'])) {
                (new TextInputHandler($updateData))->handle();
            } else {
                Log::info('Получено необрабатываемое обновление', $updateData);
            }

        } catch (\Exception $e) {
            Log::error('Критическая ошибка в TelegramBotService: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'update' => $updateData
            ]);
        }
    }
}
