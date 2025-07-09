<?php
namespace App\Telegram\Handlers;


use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class TextInputHandler extends BaseHandler
{
    public function handle(): void
    {
        $state = $this->getState();

        if ($state === 'feedback_awaiting_name') {
            $this->handleFeedbackName();
            return; // Важно завершить выполнение здесь
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

    private function handleFeedbackName(): void
    {
        // 1. Сохраняем полученное имя в контекст
        $context = $this->getContext();
        $context['feedback_name'] = $this->text;
        $this->setContext($context);

        // 2. Устанавливаем следующее состояние: "ожидаем контакт"
        $this->setState('feedback_awaiting_contact');

        // 3. Просим номер телефона с помощью специальной кнопки
        $keyboard = Keyboard::make()
            ->row([
                Keyboard::button(['text' => '📱 Отправить мой номер телефона', 'request_contact' => true]),
            ])
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true);

        $text = "Отлично, {$this->text}! Теперь, пожалуйста, нажмите на кнопку ниже, чтобы мы получили ваш номер для связи.";

        Telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => $text,
            'reply_markup' => $keyboard
        ]);
    }

}
