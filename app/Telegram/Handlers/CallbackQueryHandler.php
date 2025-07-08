<?php

namespace App\Telegram\Handlers;

use Telegram\Bot\Laravel\Facades\Telegram;

class CallbackQueryHandler extends BaseHandler
{
    public function handle(): void
    {
        $parts = explode('_', $this->callbackData);
        $action = $parts[0] ?? null;

        switch ($action) {
            // Все, что связано с каталогом, отправляем в CatalogHandler
            case 'category':
            case 'products':
            case 'back':
            case 'addtocart':
                (new CatalogHandler($this->update))->handle();
                break;

            // Все, что связано с корзиной, отправляем в CartHandler
            case 'cart':
                (new CartHandler($this->update))->handle();
                break;

            case 'checkout':
                (new CheckoutHandler($this->update))->handle();
                break;

            case 'noop':
                Telegram::answerCallbackQuery(['callback_query_id' => $this->update['callback_query']['id']]);
                break;
        }
    }
}
