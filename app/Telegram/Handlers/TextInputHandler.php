<?php
namespace App\Telegram\Handlers;

use App\Models\Product;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class TextInputHandler extends BaseHandler
{
    public function handle(): void
    {
        $state = $this->getState();

        if ($state === 'awaiting_search_query') {
            $this->performSearch();
            return; // Завершаем, чтобы не попасть в другие условия
        }
        // Если есть активное состояние оформления заказа
        if ($state && str_starts_with($state, 'checkout_')) {
            $checkoutHandler = new CheckoutHandler($this->update);

            switch ($state) {
                // Пользователь ввел номер телефона текстом
                case 'checkout_awaiting_phone':
                    $context = $this->getContext();
                    $context['phone_number'] = $this->text; // Сохраняем введенный телефон
                    $this->setContext($context);

                    $checkoutHandler->askForNewAddressText(); // Спрашиваем адрес
                    break;
                // Пользователь ввел адрес текстом
                case 'checkout_awaiting_address_text':
                    $context = $this->getContext();
                    $context['new_address_text'] = $this->text; // Сохраняем адрес
                    $this->setContext($context);
                    $checkoutHandler->askForDeliveryDate();
                //    $checkoutHandler->askForPaymentMethod(); // Спрашиваем способ оплаты
                    break;
            }
        } else {
            (new MenuHandler($this->update))->handle();
        }
    }

    private function performSearch(): void
    {
        $this->setState(null);
        $query = $this->text;

        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where('name', 'LIKE', "%{$query}%")
            ->take(20) // Ограничиваем результаты
            ->get();

        if ($products->isEmpty()) {
            Telegram::sendMessage([
                'chat_id' => $this->chatId,
                'text' => "По вашему запросу \"*{$query}*\" ничего не найдено.",
                'parse_mode' => 'Markdown',
            ]);
            return;
        }

        // Формируем клавиатуру с результатами
        $keyboard = Keyboard::make()->inline();
        foreach ($products as $product) {
            // ВАЖНО: callback_data должен быть уникальным для поиска
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => $product->name,
                    'callback_data' => 'product_show_' . $product->id . '_from_search'
                ])
            ]);
        }

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => "Вот что удалось найти по запросу \"*{$query}*\":",
            'parse_mode' => 'Markdown',
            'reply_markup' => $keyboard,
        ]);
    }


}
