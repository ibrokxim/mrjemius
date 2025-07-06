<?php
namespace App\Telegram\Handlers;

use Telegram\Bot\Laravel\Facades\Telegram;

abstract class BaseHandler
{
    protected array $update;
    protected int $chatId;
    protected ?string $text;
    protected ?string $callbackData;
    protected ?int $messageId;

    public function __construct(array $update)
    {
        $this->update = $update;
        $this->chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'];
        $this->text = $update['message']['text'] ?? null;
        $this->callbackData = $update['callback_query']['data'] ?? null;
        $this->messageId = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'] ?? null;
    }

    abstract public function handle(): void;
}
