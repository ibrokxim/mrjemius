<?php

namespace App\Telegram\Handlers;

use App\Models\Product;
use App\Models\Category;
use App\Services\CartService;
use App\Helpers\TelegramHtmlHelper;
use Telegram\Bot\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class CatalogHandler extends BaseHandler
{
    public function handle(): void
    {
        $parts = explode('_', $this->callbackData);
        $action = $parts[0] ?? null;

        // Маршрутизация внутри этого обработчика
        switch ($action) {
            case 'category':
                // При выборе категории показываем первый товар
                $categoryId = $parts[1] ?? null;
                if ($categoryId) {
                    $this->showProductCarousel($categoryId, 1, true);
                }
                break;

            case 'products':
                // Навигация по товарам в категории
                $categoryId = $parts[1] ?? null;
                $page = (int)($parts[3] ?? 1);
                if ($categoryId) {
                    $this->showProductCarousel($categoryId, $page, false);
                }
                break;

            case 'addtocart':
                $productId = $parts[1] ?? null;
                if ($productId) {
                    $this->addToCart((int)$productId);
                }
                break;


            case 'back':
                if (($parts[1] ?? null) === 'to' && ($parts[2] ?? null) === 'categories') {
                    $this->backToCategories();
                }
                break;
        }
    }

    /**
     * Показывает один товар из категории с пагинацией (карусель).
     */
    protected function showProductCarousel($categoryId, $page = 1, $isFirstPage = false): void
    {
        try {
            $category = Category::find($categoryId);
            if (!$category) {
                $this->showError('Категория не найдена.');
                return;
            }

            // Получаем все активные товары в категории
            $products = $category->products()->where('is_active', true)->get();

            if ($products->isEmpty()) {
                $this->showEmptyCategoryMessage();
                return;
            }

            // Проверяем, что запрашиваемая страница существует
            if ($page < 1 || $page > $products->count()) {
                $page = 1;
            }

            // Получаем текущий товар (страница = позиция в коллекции)
            $currentProduct = $products->skip($page - 1)->first();
            $totalProducts = $products->count();

            // Формируем подпись к товару
            $productName = TelegramHtmlHelper::escapeHtml($currentProduct->getTranslation('name', 'ru'));
            $caption = "<b>{$productName}</b>\n\n";

            // Добавляем описание, если есть
            if ($currentProduct->description) {
                $description = $currentProduct->getTranslation('description', 'ru');
                // Очищаем HTML и обрезаем до 200 символов
                $description = TelegramHtmlHelper::stripAllTags($description);
                $description = TelegramHtmlHelper::truncateText($description, 200);
                $caption .= "{$description}\n\n";
            }

            $caption .= "💰 Цена: " . TelegramHtmlHelper::formatPrice($currentProduct->price) . "\n";

            // Добавляем информацию о наличии
            if ($currentProduct->stock_quantity > 0) {
                $caption .= "📦 В наличии: {$currentProduct->stock_quantity} шт.";
            } else {
                $caption .= "❌ Нет в наличии";
            }

            // Аутентифицируем пользователя для проверки корзины
            auth()->login($this->user);
            $keyboard = $this->buildProductKeyboard(
                new CartService(),
                $currentProduct->id,
                $categoryId,
                $page,
                $totalProducts
            );
            auth()->logout();

            // Подготавливаем фото товара
            $photoFile = $this->preparePhoto($currentProduct);

            // Отправляем или редактируем сообщение
            if ($isFirstPage) {
                // Удаляем предыдущее сообщение и отправляем новое
                try {
                    Telegram::deleteMessage([
                        'chat_id' => $this->chatId,
                        'message_id' => $this->messageId
                    ]);
                } catch (\Exception $e) {
                    // Игнорируем ошибку, если сообщение уже удалено
                }

                Telegram::sendPhoto([
                    'chat_id' => $this->chatId,
                    'photo' => $photoFile,
                    'caption' => $caption,
                    'parse_mode' => 'HTML',
                    'reply_markup' => $keyboard
                ]);
            } else {
                // Пытаемся редактировать существующее сообщение
                try {
                    Telegram::editMessageMedia([
                        'chat_id' => $this->chatId,
                        'message_id' => $this->messageId,
                        'media' => [
                            'type' => 'photo',
                            'media' => $photoFile,
                            'caption' => $caption,
                            'parse_mode' => 'HTML'
                        ],
                        'reply_markup' => $keyboard
                    ]);
                } catch (\Exception $e) {
                    // Если не удалось отредактировать, удаляем и отправляем новое
                    try {
                        Telegram::deleteMessage([
                            'chat_id' => $this->chatId,
                            'message_id' => $this->messageId
                        ]);
                    } catch (\Exception $deleteError) {
                        // Игнорируем ошибку удаления
                    }

                    Telegram::sendPhoto([
                        'chat_id' => $this->chatId,
                        'photo' => $photoFile,
                        'caption' => $caption,
                        'parse_mode' => 'HTML',
                        'reply_markup' => $keyboard
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error("Telegram showProductCarousel error: " . $e->getMessage());
            $this->showError('Произошла ошибка при загрузке товара.');
        }
    }

    /**
     * Добавление товара в корзину
     */
    protected function addToCart(int $productId): void
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                Telegram::answerCallbackQuery([
                    'callback_query_id' => $this->update['callback_query']['id'],
                    'text' => 'Товар не найден!',
                    'show_alert' => true
                ]);
                return;
            }

            if ($product->stock_quantity < 1) {
                Telegram::answerCallbackQuery([
                    'callback_query_id' => $this->update['callback_query']['id'],
                    'text' => 'Этого товара нет в наличии!',
                    'show_alert' => true
                ]);
                return;
            }

            // Добавляем товар в корзину
            auth()->login($this->user);
            $cartService = new CartService();
            $cartService->add($productId, 1);
            auth()->logout();

            // Отправляем уведомление
            Telegram::answerCallbackQuery([
                'callback_query_id' => $this->update['callback_query']['id'],
                'text' => "✅ {$product->getTranslation('name', 'ru')} добавлен в корзину!"
            ]);

            // Обновляем текущий товар, чтобы показать изменение кнопки
            $this->refreshCurrentProduct($product);

        } catch (\Exception $e) {
            Log::error("Telegram addToCart error: " . $e->getMessage());
            Telegram::answerCallbackQuery([
                'callback_query_id' => $this->update['callback_query']['id'],
                'text' => 'Произошла ошибка при добавлении в корзину.',
                'show_alert' => true
            ]);
        }
    }
    public static function showProduct(int $chatId, int $productId, ?int $messageId = null, ?string $backCallback = 'back_to_categories'): void
    {
        $product = Product::with('primaryImage')->find($productId);
        if (!$product) {
            Telegram::sendMessage(['chat_id' => $chatId, 'text' => 'Товар не найден.']);
            return;
        }

        $price = number_format($product->price, 0, '.', ' ');
        $text = "<b>{$product->name}</b>\n\n";
        if ($product->description) {
            $description = strip_tags($product->description);
            $maxLength = 900;
            if (mb_strlen($description) > $maxLength) {
                $description = mb_substr($description, 0, $maxLength) . '...';
            }
            $text .= $description . "\n\n";
        }
        $text .= "Цена: {$price} сум";

        $keyboard = Keyboard::make()->inline()->row([
            Keyboard::inlineButton(['text' => '🛒 Добавить в корзину', 'callback_data' => 'addtocart_' . $product->id . '_1'])
        ]);
        // Кнопка назад. Для поиска она может вести просто в главное меню категорий.
        $keyboard->row([
            Keyboard::inlineButton(['text' => '⬅️ Назад к категориям', 'callback_data' => $backCallback])
        ]);

        $params = [
            'chat_id' => $chatId,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboard,
        ];

        if ($product->primaryImage) {
            $photoUrl = asset('storage/' . $product->primaryImage->image_url);
            $params['photo'] = InputFile::create($photoUrl, $product->name);
            $params['caption'] = $text;
        } else {
            $params['text'] = $text;
        }

        if ($messageId) {
            try { Telegram::deleteMessage(['chat_id' => $chatId, 'message_id' => $messageId]); } catch (Exception $e) {}
        }

        if (isset($params['photo'])) {
            Telegram::sendPhoto($params);
        } else {
            Telegram::sendMessage($params);
        }
    }

    /**
     * Обновляет текущий товар после добавления в корзину
     */
    private function refreshCurrentProduct(Product $product): void
    {
        // Находим текущую позицию товара в категории
        $categoryProducts = $product->category->products()->where('is_active', true)->get();
        $currentPosition = $categoryProducts->search(function ($item) use ($product) {
                return $item->id === $product->id;
            }) + 1;

        // Обновляем карусель
        $this->showProductCarousel($product->category_id, $currentPosition, false);
    }

    /**
     * Создает клавиатуру для товара
     */
    private function buildProductKeyboard(CartService $cartService, int $productId, int $categoryId, int $currentPage, int $totalProducts): Keyboard
    {
        $keyboard = Keyboard::make()->inline();

        // Проверяем, есть ли товар в корзине
        $isInCart = $cartService->itemExists($productId, $this->user->id);

        // Получаем товар для проверки наличия
        $product = Product::find($productId);

        // Кнопка добавления в корзину или индикатор "в корзине"
        if (!$product || $product->stock_quantity < 1) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => '❌ Нет в наличии',
                    'callback_data' => 'noop'
                ])
            ]);
        } elseif ($isInCart) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => '✅ Уже в корзине',
                    'callback_data' => 'noop'
                ])
            ]);
        } else {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => '🛒 Добавить в корзину',
                    'callback_data' => 'addtocart_' . $productId
                ])
            ]);
        }

        // Кнопки навигации (только если товаров больше одного)
        if ($totalProducts > 1) {
            $navigationRow = [];

            // Кнопка "Назад" (к предыдущему товару)
            if ($currentPage > 1) {
                $navigationRow[] = Keyboard::inlineButton([
                    'text' => '⬅️ Назад',
                    'callback_data' => "products_{$categoryId}_page_" . ($currentPage - 1)
                ]);
            }

            // Счетчик страниц
            $navigationRow[] = Keyboard::inlineButton([
                'text' => "{$currentPage} / {$totalProducts}",
                'callback_data' => 'noop'
            ]);

            // Кнопка "Вперед" (к следующему товару)
            if ($currentPage < $totalProducts) {
                $navigationRow[] = Keyboard::inlineButton([
                    'text' => 'Вперед ➡️',
                    'callback_data' => "products_{$categoryId}_page_" . ($currentPage + 1)
                ]);
            }

            $keyboard->row($navigationRow);
        }

        // Кнопка "Назад к категориям"
        $keyboard->row([
            Keyboard::inlineButton([
                'text' => '🔙 Назад к категориям',
                'callback_data' => 'back_to_categories'
            ])
        ]);

        return $keyboard;
    }

    /**
     * Подготавливает фото товара
     */
    private function preparePhoto($product)
    {
        if ($product->primaryImage && $product->primaryImage->image_url) {
            $localPath = storage_path('app/public/' . $product->primaryImage->image_url);
            if (file_exists($localPath)) {
                return InputFile::create($localPath, $product->name . '.jpg');
            }
        }

        // Возвращаем placeholder, если фото нет
        return InputFile::create('https://via.placeholder.com/400x300/cccccc/666666?text=No+Image', 'placeholder.jpg');
    }

    /**
     * Показывает сообщение о пустой категории
     */
    private function showEmptyCategoryMessage(): void
    {
        $backButton = Keyboard::make()->inline()->row([
            Keyboard::inlineButton([
                'text' => '🔙 Назад к категориям',
                'callback_data' => 'back_to_categories'
            ])
        ]);

        try {
            Telegram::editMessageText([
                'chat_id' => $this->chatId,
                'message_id' => $this->messageId,
                'text' => '📭 В этой категории пока нет товаров.',
                'reply_markup' => $backButton
            ]);
        } catch (\Exception $e) {
            // Если не удалось отредактировать, отправляем новое сообщение
            Telegram::sendMessage([
                'chat_id' => $this->chatId,
                'text' => '📭 В этой категории пока нет товаров.',
                'reply_markup' => $backButton
            ]);
        }
    }

    /**
     * Показывает сообщение об ошибке
     */
    private function showError(string $message): void
    {
        $backButton = Keyboard::make()->inline()->row([
            Keyboard::inlineButton([
                'text' => '🔙 Назад к категориям',
                'callback_data' => 'back_to_categories'
            ])
        ]);

        try {
            Telegram::editMessageText([
                'chat_id' => $this->chatId,
                'message_id' => $this->messageId,
                'text' => "❌ {$message}",
                'reply_markup' => $backButton
            ]);
        } catch (\Exception $e) {
            // Если не удалось отредактировать, отправляем новое сообщение
            Telegram::sendMessage([
                'chat_id' => $this->chatId,
                'text' => "❌ {$message}",
                'reply_markup' => $backButton
            ]);
        }
    }

    /**
     * Возвращает к списку категорий
     */
    protected function backToCategories(): void
    {
        try {
            Telegram::deleteMessage([
                'chat_id' => $this->chatId,
                'message_id' => $this->messageId
            ]);
        } catch (\Exception $e) {
            // Игнорируем ошибку, если сообщение уже удалено
        }

        MenuHandler::showCategories($this->chatId);
    }
}
