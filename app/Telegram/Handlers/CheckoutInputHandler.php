<?php
// Файл: app/Telegram/Handlers/CheckoutInputHandler.php
namespace App\Telegram\Handlers;


class CheckoutInputHandler extends BaseHandler
{
    public function handle(): void
    {
        $state = $this->getState();

        switch ($state) {
            case 'checkout_awaiting_phone':
                $this->handlePhoneInput();
                break;
            case 'checkout_awaiting_address_text':
                $this->handleAddressInput();
                break;
        }
    }

    private function handlePhoneInput(): void
    {
        $context = $this->getContext();
        $context['phone_number'] = $this->text; // Сохраняем телефон
        $this->setContext($context);

        (new CheckoutHandler($this->update))->askForAddress(); // Запрашиваем адрес
    }

    private function handleAddressInput(): void
    {
        $context = $this->getContext();
        $context['new_address_text'] = $this->text; // Сохраняем текст адреса
        $this->setContext($context);

        (new CheckoutHandler($this->update))->askForPaymentMethod(); // Запрашиваем способ оплаты
    }

}
