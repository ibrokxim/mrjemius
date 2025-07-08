<?php
// Файл: app/Services/TelegramBotService.php

namespace App\Services;

use App\Telegram\Handlers\CallbackQueryHandler;
use App\Telegram\Handlers\MenuHandler;
use App\Telegram\Handlers\CheckoutInputHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    public function handleUpdate(array $updateData): void
    {
        try {
            // 1. Если это нажатие на inline-кнопку (callback_query)
            if (isset($updateData['callback_query'])) {
                (new CallbackQueryHandler($updateData))->handle();
                return; // Завершаем обработку
            }

            // 2. Если это текстовое сообщение
            if (isset($updateData['message']['text'])) {
                $chatId = $updateData['message']['chat']['id'];
                $state = Cache::get('bot_state_' . $chatId);

                // Если у пользователя есть активное состояние (оформление заказа)
                if ($state && str_starts_with($state, 'checkout_')) {
                    (new CheckoutInputHandler($updateData))->handle();
                } else {
                    // Иначе, это команда из главного меню
                    (new MenuHandler($updateData))->handle();
                }
                return; // Завершаем обработку
            }

            // 3. Здесь можно будет добавить обработку контактов, геолокации и т.д.
            Log::info('Получено необрабатываемое обновление', $updateData);

        } catch (\Exception $e) {
            Log::error('Критическая ошибка в TelegramBotService: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'update' => $updateData
            ]);
        }
    }
}
