<?php
namespace App\Telegram\Handlers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

abstract class BaseHandler
{
    protected array $update;
    protected int $chatId;
    protected ?string $text = null;
    protected ?string $callbackData = null;
    protected ?int $messageId;
    protected User $user;

    public function __construct(array $update)
    {
        $this->update = $update;
        $fromData = $update['message']['from'] ?? $update['callback_query']['from'] ?? null;
        if (!$fromData) {
            throw new \Exception('Не удалось определить пользователя из данных Telegram.');
        }
        $this->chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'] ?? $fromData['id'];

        if (isset($update['message'])) {
            $this->messageId = $update['message']['message_id'];
            if (isset($update['message']['text'])) {
                $this->text = $update['message']['text'];
            }
        } elseif (isset($update['callback_query'])) {
            $this->messageId = $update['callback_query']['message']['message_id'];
            $this->callbackData = $update['callback_query']['data'];
        } else {
            $this->messageId = null;
        }

        $this->user = User::firstOrCreate(
            ['telegram_id' => $fromData['id']],
            [
                'name' => $fromData['first_name'] ?? 'Пользователь ' . $fromData['id'],
                'telegram_username' => $fromData['username'] ?? null,
            ]
        );
    }

    abstract public function handle(): void;

    protected function setState(?string $state, int $minutes = 15): void
    {
        $key = 'bot_state_' . $this->chatId;
        if ($state) {
            Cache::put($key, $state, now()->addMinutes($minutes));
        } else {
            Cache::forget($key);
        }
    }

    protected function getState(): ?string
    {
        return Cache::get('bot_state_' . $this->chatId);
    }

    protected function setContext(array $data, int $minutes = 15): void
    {
        $key = 'bot_context_' . $this->chatId;
        Cache::put($key, $data, now()->addMinutes($minutes));
    }

    protected function getContext(): array
    {
        return Cache::get('bot_context_' . $this->chatId, []);
    }
}
