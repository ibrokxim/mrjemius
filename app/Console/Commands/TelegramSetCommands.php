<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramSetCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-commands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Устанавливает список команд для Telegram бота';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Установка команд для Telegram бота...');

        try {
            // Список наших команд
            $commands = [
                ['command' => 'start', 'description' => '🚀 Запустить / Перезагрузить бота'],
                ['command' => 'catalog', 'description' => '🛍️ Открыть каталог'],
                ['command' => 'cart', 'description' => '🛒 Посмотреть корзину'],
                ['command' => 'myorders', 'description' => '👤 Мои заказы'],
                ['command' => 'support', 'description' => '📞 Обратная связь'],
            ];

            // Отправляем запрос в Telegram API
            $response = Telegram::setMyCommands(['commands' => $commands]);

            if ($response) {
                $this->info('Команды успешно установлены!');
                $this->comment('Может потребоваться несколько минут, чтобы изменения вступили в силу. Перезапустите ваш клиент Telegram, чтобы увидеть их быстрее.');
            } else {
                $this->error('Не удалось установить команды.');
            }

        } catch (\Exception $e) {
            $this->error('Произошла ошибка: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
