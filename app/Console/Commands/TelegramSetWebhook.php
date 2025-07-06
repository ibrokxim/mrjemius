<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramSetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook';
    protected $description = 'Sets the Telegram webhook URL';

    // Файл: app/Console/Commands/TelegramSetWebhook.php

    public function handle()
    {
        // Убедимся, что URL генерируется правильно для продакшена
        $url = route('telegram.webhook');

        try {
            // Метод setWebhook возвращает true или выбрасывает исключение
            $response = Telegram::setWebhook(['url' => $url]);

            if ($response) {
                $this->info("Webhook успешно установлен на URL: {$url}");

                // Дополнительно можно запросить информацию о вебхуке для проверки
                $webhookInfo = Telegram::getWebhookInfo();
                $this->info("Текущий URL вебхука по данным Telegram: " . $webhookInfo->getUrl());

            } else {
                $this->error("Не удалось установить вебхук. Метод вернул false.");
            }

        } catch (\Exception $e) {
            $this->error("Не удалось установить вебхук: " . $e->getMessage());
        }
    }
}
