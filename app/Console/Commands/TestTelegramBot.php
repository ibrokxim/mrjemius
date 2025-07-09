<?php
// Файл: app/Console/Commands/TestTelegramBot.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;

class TestTelegramBot extends Command
{
    protected $signature = 'telegram:test {chat_id?}';
    protected $description = 'Test Telegram bot connection';

    public function handle()
    {
        try {
            // Проверяем подключение к API
            $this->info('Testing Telegram API connection...');

            $me = Telegram::getMe();
            $this->info('Bot info: ' . $me->getFirstName() . ' (@' . $me->getUsername() . ')');

            // Получаем информацию о webhook
            $webhookInfo = Telegram::getWebhookInfo();
            $this->info('Webhook URL: ' . $webhookInfo->getUrl());
            $this->info('Webhook pending updates: ' . $webhookInfo->getPendingUpdateCount());

            if ($webhookInfo->getLastErrorMessage()) {
                $this->error('Webhook error: ' . $webhookInfo->getLastErrorMessage());
            }

            // Если указан chat_id, отправляем тестовое сообщение
            if ($chatId = $this->argument('chat_id')) {
                $this->info('Sending test message to chat: ' . $chatId);

                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Тестовое сообщение от бота! 🤖'
                ]);

                $this->info('Message sent successfully! Message ID: ' . $response->getMessageId());
            }

            // Проверяем конфигурацию
            $this->info('Bot token configured: ' . (config('telegram.bots.mybot.token') ? 'Yes' : 'No'));
            $this->info('Admin chat ID: ' . (env('TELEGRAM_ADMIN_CHAT_ID') ?: 'Not set'));

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Telegram test error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
}
