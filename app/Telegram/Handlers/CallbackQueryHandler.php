<?php
// Файл: app/Telegram/Handlers/CallbackQueryHandler.php

namespace App\Telegram\Handlers;

use App\Models\Category;
use Illuminate\Support\Facades\Log; // <-- Убедитесь, что Log импортирован
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class CallbackQueryHandler extends BaseHandler
{
    public function handle(): void
    {
        $parts = explode('_', $this->callbackData);
        $action = $parts[0] ?? null;

        // Отвечаем Telegram сразу, чтобы убрать "часики"
        try {
            Telegram::answerCallbackQuery(['callback_query_id' => $this->update['callback_query']['id']]);
        } catch (\Exception $e) {
            // Игнорируем ошибку, если запрос уже устарел
        }

        switch ($action) {
            case 'category':
            case 'products': // Объединяем обработку
                $categoryId = $parts[1] ?? null;
                $page = (int)($parts[3] ?? 1);
                if ($categoryId) {
                    $isFirstPage = ($action === 'category');
                    $this->showProducts($categoryId, $page, $isFirstPage);
                }
                break;
            case 'back':
                if (($parts[1] ?? null) === 'to' && ($parts[2] ?? null) === 'categories') {
                    $this->backToCategories();
                }
                break;
        }
    }

    protected function showProducts($categoryId, $page = 1, $isFirstPage = false): void
    {
        try {
            $category = Category::find($categoryId);
            if (!$category) {
                Log::warning("Telegram: Категория с ID {$categoryId} не найдена.");
                return;
            }

            // --- ИЗМЕНЕНИЕ: Убираем paginate(), используем skip/take ---
            $perPage = 1; // По одному товару на "странице"
            $allProducts = $category->products()->where('is_active', true)->get();
            $totalProducts = $allProducts->count();

            if ($totalProducts === 0) {
                $this->showEmptyCategoryMessage();
                return;
            }

            // Рассчитываем пагинацию вручную
            $lastPage = ceil($totalProducts / $perPage);
            $page = max(1, min($page, $lastPage)); // Убедимся, что страница в пределах допустимого
            $currentItem = $allProducts->slice(($page - 1) * $perPage, $perPage)->first();

            if (!$currentItem) {
                Log::warning("Telegram: Товар не найден для категории {$categoryId} на странице {$page}.");
                return;
            }

            // Формируем подпись к фото
            $caption = "<b>{$currentItem->name}</b>\n\n";
            $caption .= "Цена: " . number_format($currentItem->price, 0, '.', ' ') . " сум";

            // Формируем клавиатуру
            $keyboard = $this->buildProductKeyboard($currentItem->id, $categoryId, $page, $lastPage);

            // Готовим фото для отправки
            $photoFile = $this->preparePhoto($currentItem);

            // Отправляем или редактируем сообщение
            if ($isFirstPage) {
                Telegram::deleteMessage(['chat_id' => $this->chatId, 'message_id' => $this->messageId]);
                Telegram::sendPhoto(['chat_id' => $this->chatId, 'photo' => $photoFile, 'caption' => $caption, 'parse_mode' => 'HTML', 'reply_markup' => $keyboard]);
            } else {
                Telegram::editMessageMedia(['chat_id' => $this->chatId, 'message_id' => $this->messageId, 'media' => ['type' => 'photo', 'media' => $photoFile, 'caption' => $caption, 'parse_mode' => 'HTML'], 'reply_markup' => $keyboard]);
            }
        } catch (\Exception $e) {
            Log::error("Telegram showProducts error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        }
    }

    // --- НОВЫЕ ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ ДЛЯ ЧИСТОТЫ КОДА ---

    private function buildProductKeyboard(int $productId, int $categoryId, int $currentPage, int $lastPage): Keyboard
    {
        $keyboard = Keyboard::make()->inline();
        $keyboard->row([Keyboard::inlineButton(['text' => '➕ Добавить в корзину', 'callback_data' => 'addtocart_' . $productId])]);

        $paginationRow = [];
        if ($currentPage > 1) {
            $paginationRow[] = Keyboard::inlineButton(['text' => '◀️', 'callback_data' => "products_{$categoryId}_page_" . ($currentPage - 1)]);
        }
        if ($lastPage > 1) { // Показываем номер страницы, только если их больше одной
            $paginationRow[] = Keyboard::inlineButton(['text' => "{$currentPage} / {$lastPage}", 'callback_data' => 'noop']);
        }
        if ($currentPage < $lastPage) {
            $paginationRow[] = Keyboard::inlineButton(['text' => '▶️', 'callback_data' => "products_{$categoryId}_page_" . ($currentPage + 1)]);
        }
        if (!empty($paginationRow)) {
            $keyboard->row($paginationRow);
        }

        $keyboard->row([Keyboard::inlineButton(['text' => '⬅️ Назад к категориям', 'callback_data' => 'back_to_categories'])]);
        return $keyboard;
    }

    private function preparePhoto($product)
    {
        if ($product->primaryImage && $product->primaryImage->image_url) {
            $localPath = storage_path('app/public/' . $product->primaryImage->image_url);
            if (file_exists($localPath)) {
                return InputFile::create($localPath, $product->name . '.jpg');
            }
        }
        // Возвращаем заглушку, если фото нет или файл не найден
        return InputFile::create('https://via.placeholder.com/350', 'placeholder.jpg');
    }

    private function showEmptyCategoryMessage(): void
    {
        $backButton = Keyboard::make()->inline()->row([Keyboard::inlineButton(['text' => '⬅️ Назад к категориям', 'callback_data' => 'back_to_categories'])]);
        Telegram::editMessageText(['chat_id' => $this->chatId, 'message_id' => $this->messageId, 'text' => 'В этой категории пока нет товаров.', 'reply_markup' => $backButton]);
    }

    protected function backToCategories(): void
    {
        Telegram::deleteMessage(['chat_id' => $this->chatId, 'message_id' => $this->messageId]);
        (new MenuHandler($this->update))->showCategories();
    }
}
