<?php
namespace App\Telegram\Handlers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

abstract class BaseHandler
{
    protected array $update;
    protected int $chatId;
    protected ?string $text;
    protected ?string $callbackData;
    protected ?int $messageId;
    protected User $user;

    public function __construct(array $update)
    {
        $this->update = $update;
        $fromData = $update['message']['from'] ?? $update['callback_query']['from'];
        $this->chatId = $fromData['id'];

        //$this->chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'];
        $this->text = $update['message']['text'] ?? null;
        $this->callbackData = $update['callback_query']['data'] ?? null;
        $this->messageId = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'] ?? null;

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
