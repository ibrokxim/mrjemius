<?php

namespace App\Helpers;

class TelegramHtmlHelper
{
    /**
     * Разрешенные HTML-теги для Telegram
     */
    private static array $allowedTags = [
        'b', 'i', 'u', 's', 'code', 'pre', 'a'
    ];

    /**
     * Очищает HTML-контент для безопасного использования в Telegram
     */
    public static function sanitizeHtml(string $html): string
    {
        // Сначала декодируем HTML-сущности
        $text = html_entity_decode($html, ENT_QUOTES, 'UTF-8');

        // Заменяем некоторые HTML-теги на поддерживаемые Telegram
        $replacements = [
            '<p>' => "\n",
            '</p>' => "\n",
            '<br>' => "\n",
            '<br/>' => "\n",
            '<br />' => "\n",
            '<strong>' => '<b>',
            '</strong>' => '</b>',
            '<em>' => '<i>',
            '</em>' => '</i>',
            '<h1>' => '<b>',
            '</h1>' => "</b>\n",
            '<h2>' => '<b>',
            '</h2>' => "</b>\n",
            '<h3>' => '<b>',
            '</h3>' => "</b>\n",
            '<h4>' => '<b>',
            '</h4>' => "</b>\n",
            '<h5>' => '<b>',
            '</h5>' => "</b>\n",
            '<h6>' => '<b>',
            '</h6>' => "</b>\n",
        ];

        $text = str_ireplace(array_keys($replacements), array_values($replacements), $text);

        // Удаляем все остальные HTML-теги, кроме разрешенных
        $text = strip_tags($text, '<' . implode('><', self::$allowedTags) . '>');

        // Убираем лишние переносы строк
        $text = preg_replace('/\n\s*\n/', "\n\n", $text);
        $text = trim($text);

        return $text;
    }

    /**
     * Полностью очищает текст от HTML-тегов
     */
    public static function stripAllTags(string $html): string
    {
        $text = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        $text = strip_tags($text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    /**
     * Экранирует специальные символы для HTML-режима Telegram
     */
    public static function escapeHtml(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Форматирует цену для отображения
     */
    public static function formatPrice(float $price, string $currency = 'сум'): string
    {
        return number_format($price, 0, '.', ' ') . ' ' . $currency;
    }

    /**
     * Обрезает текст до указанной длины с добавлением многоточия
     */
    public static function truncateText(string $text, int $maxLength = 200): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        return mb_substr($text, 0, $maxLength) . '...';
    }

    /**
     * Проверяет, является ли строка валидным HTML для Telegram
     */
    public static function isValidTelegramHtml(string $html): bool
    {
        // Простая проверка на наличие неподдерживаемых тегов
        $unsupportedTags = [
            'div', 'span', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'img', 'video', 'audio', 'table', 'tr', 'td', 'th',
            'ul', 'ol', 'li', 'form', 'input', 'button', 'script', 'style'
        ];

        foreach ($unsupportedTags as $tag) {
            if (stripos($html, "<{$tag}") !== false) {
                return false;
            }
        }

        return true;
    }
}
