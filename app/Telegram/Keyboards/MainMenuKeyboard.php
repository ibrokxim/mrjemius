<?php

namespace App\Telegram\Keyboards;
use Telegram\Bot\Keyboard\Keyboard;

class MainMenuKeyboard
{
    public static function build(): Keyboard
    {
        return Keyboard::make()
            ->row([
                Keyboard::button(['text' => '🛍 Каталог']),
                Keyboard::button(['text' => '🛒 Корзина']),
            ])
            ->row([
                Keyboard::button(['text' => '👤 Мои заказы']),
//                Keyboard::button(['text' => '❤️ Избранное']),
            ])
            ->row([
                Keyboard::button(['text' => '📞 Обратная связь']),
                Keyboard::button(['text' => 'ℹ️ О нас', 'web_app' => ['url' => 'https://mrdjemiuszero.uz/about']]),
            ])
            ->setResizeKeyboard(true);
    }
}
