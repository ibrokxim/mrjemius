<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Отображение корзины
     */
    public function index()
    {
        if (Auth::check()) {
            $cartItems = CartItem::with(['product', 'product.primaryImage']) // Загружаем изображение
            ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc') // Или по другому полю
                ->get();
        } else {
            $sessionId = Session::getId();
            $cartItems = CartItem::with(['product', 'product.primaryImage'])
                ->where('session_id', $sessionId)
                ->whereNull('user_id')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $subtotal = $cartItems->sum(function ($item) {
            $price = $item->product->sell_price ?? $item->product->price;
            return $price * $item->quantity;
        });

        // Пример расчета других сумм, если нужно
        $serviceFee = 3000; // Пример, сумов (замените на вашу логику)
        $total = $subtotal + $serviceFee; // Или $subtotal, если нет доп. сборов

        // Для сообщения о доставке
        $freeShippingThreshold = 500000; // Ваш порог
        $needsForFreeShipping = $freeShippingThreshold - $subtotal > 0 ? $freeShippingThreshold - $subtotal : 0;


        return view('cart.index', [ // Убедитесь, что имя файла cart.index.blade.php
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'serviceFee' => $serviceFee, // Если есть
            'total' => $total,
            'needsForFreeShipping' => $needsForFreeShipping,
            'freeShippingThreshold' => $freeShippingThreshold
        ]);
    }

    /**
     * Добавление товара в корзину
     */
    public function add(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'quantity' => 'integer|min:1|max:100'
        ]);

        $quantity = $request->get('quantity', 1);

        // Проверяем наличие товара на складе
        if ($product->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно товара на складе. Доступно: ' . $product->stock_quantity
            ], 400);
        }

        if (Auth::check()) {
            // Для авторизованных пользователей
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $quantity;
                if ($newQuantity > $product->stock_quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Общее количество превышает доступный остаток'
                    ], 400);
                }
                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                CartItem::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $quantity
                ]);
            }
        } else {
            // Для неавторизованных пользователей (гостей)
            $sessionId = Session::getId();
            $cartItem = CartItem::where('session_id', $sessionId)
                ->where('product_id', $product->id)
                ->whereNull('user_id')
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $quantity;
                if ($newQuantity > $product->stock_quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Общее количество превышает доступный остаток'
                    ], 400);
                }
                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                CartItem::create([
                    'session_id' => $sessionId,
                    'product_id' => $product->id,
                    'quantity' => $quantity
                ]);
            }
        }

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Товар добавлен в корзину',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Обновление количества товара в корзине
     */
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100'
        ]);

        $quantity = $request->get('quantity');

        // Проверяем права доступа
        if (!$this->canAccessCartItem($cartItem)) {
            return response()->json([
                'success' => false,
                'message' => 'Нет доступа к данному элементу корзины'
            ], 403);
        }

        // Проверяем наличие на складе
        if ($cartItem->product->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно товара на складе. Доступно: ' . $cartItem->product->stock_quantity
            ], 400);
        }

        $cartItem->update(['quantity' => $quantity]);

        $newTotal = $this->getCartTotal();
        $itemTotal = ($cartItem->product->sell_price ?? $cartItem->product->price) * $quantity;

        return response()->json([
            'success' => true,
            'message' => 'Количество обновлено',
            'item_total' => number_format($itemTotal, 0, ',', ' '),
            'cart_total' => number_format($newTotal, 0, ',', ' ')
        ]);
    }

    /**
     * Удаление товара из корзины
     */
    public function remove(CartItem $cartItem): JsonResponse
    {
        // Проверяем права доступа
        if (!$this->canAccessCartItem($cartItem)) {
            return response()->json([
                'success' => false,
                'message' => 'Нет доступа к данному элементу корзины'
            ], 403);
        }

        $cartItem->delete();

        $cartCount = $this->getCartCount();
        $newTotal = $this->getCartTotal();

        return response()->json([
            'success' => true,
            'message' => 'Товар удален из корзины',
            'cart_count' => $cartCount,
            'cart_total' => number_format($newTotal, 0, ',', ' ')
        ]);
    }

    /**
     * Очистка корзины
     */
    public function clear(): JsonResponse
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();
        } else {
            $sessionId = Session::getId();
            CartItem::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Корзина очищена'
        ]);
    }

    /**
     * Перенос товаров из избранного в корзину
     */
    public function moveFromWishlist(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо войти в систему'
            ], 401);
        }

        $wishlistItems = WishlistItem::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($wishlistItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Список избранного пуст'
            ], 400);
        }

        $addedCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($wishlistItems, &$addedCount, &$skippedCount) {
            foreach ($wishlistItems as $wishlistItem) {
                $product = $wishlistItem->product;

                // Проверяем наличие на складе
                if ($product->stock_quantity < 1) {
                    $skippedCount++;
                    continue;
                }

                // Проверяем, есть ли уже товар в корзине
                $existingCartItem = CartItem::where('user_id', Auth::id())
                    ->where('product_id', $product->id)
                    ->first();

                if ($existingCartItem) {
                    // Увеличиваем количество, если можем
                    if ($existingCartItem->quantity < $product->stock_quantity) {
                        $existingCartItem->increment('quantity');
                        $addedCount++;
                    } else {
                        $skippedCount++;
                    }
                } else {
                    // Добавляем новый товар в корзину
                    CartItem::create([
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'quantity' => 1
                    ]);
                    $addedCount++;
                }

                // Удаляем из избранного
                $wishlistItem->delete();
            }
        });

        $message = "Перенесено в корзину: {$addedCount} товаров";
        if ($skippedCount > 0) {
            $message .= ", пропущено: {$skippedCount} товаров";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'added_count' => $addedCount,
            'skipped_count' => $skippedCount,
            'cart_count' => $this->getCartCount()
        ]);
    }

    /**
     * Получение количества товаров в корзине
     */
    public function getCartCount(): int
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id())->sum('quantity');
        } else {
            $sessionId = Session::getId();
            return CartItem::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->sum('quantity');
        }
    }

    /**
     * Получение общей стоимости корзины
     */
    private function getCartTotal(): float
    {
        if (Auth::check()) {
            $cartItems = CartItem::with('product')
                ->where('user_id', Auth::id())
                ->get();
        } else {
            $sessionId = Session::getId();
            $cartItems = CartItem::with('product')
                ->where('session_id', $sessionId)
                ->whereNull('user_id')
                ->get();
        }

        return $cartItems->sum(function ($item) {
            $price = $item->product->sell_price ?? $item->product->price;
            return $price * $item->quantity;
        });
    }

    /**
     * Проверка доступа к элементу корзины
     */
    private function canAccessCartItem(CartItem $cartItem): bool
    {
        if (Auth::check()) {
            return $cartItem->user_id === Auth::id();
        } else {
            $sessionId = Session::getId();
            return $cartItem->session_id === $sessionId && is_null($cartItem->user_id);
        }
    }

    /**
     * Получение данных корзины для offcanvas
     */
    public function getCartData(): JsonResponse
    {
        if (Auth::check()) {
            $cartItems = CartItem::with(['product', 'product.primaryImage'])
                ->where('user_id', Auth::id())
                ->get();
        } else {
            $sessionId = Session::getId();
            $cartItems = CartItem::with(['product', 'product.primaryImage'])
                ->where('session_id', $sessionId)
                ->whereNull('user_id')
                ->get();
        }

        $total = $cartItems->sum(function ($item) {
            $price = $item->product->sell_price ?? $item->product->price;
            return $price * $item->quantity;
        });

        $cartCount = $cartItems->sum('quantity');

        return response()->json([
            'items' => $cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product->id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'price' => $item->product->sell_price ?? $item->product->price,
                    'original_price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'image' => $item->product->primaryImage ? asset('storage/' . $item->product->primaryImage->image_url) : asset('assets/images/placeholder.png'),
                    'total' => ($item->product->sell_price ?? $item->product->price) * $item->quantity
                ];
            }),
            'total' => $total,
            'count' => $cartCount,
            'formatted_total' => number_format($total, 0, ',', ' ') . ' сум'
        ]);
    }

    /**
     * Миграция корзины гостя к авторизованному пользователю
     */
    public function migrateGuestCart(): void
    {
        if (!Auth::check()) {
            return;
        }

        $sessionId = Session::getId();
        $guestCartItems = CartItem::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();

        if ($guestCartItems->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($guestCartItems) {
            foreach ($guestCartItems as $guestItem) {
                $existingUserItem = CartItem::where('user_id', Auth::id())
                    ->where('product_id', $guestItem->product_id)
                    ->first();

                if ($existingUserItem) {
                    // Объединяем количество
                    $newQuantity = $existingUserItem->quantity + $guestItem->quantity;
                    $maxQuantity = $guestItem->product->stock_quantity;
                    $existingUserItem->update([
                        'quantity' => min($newQuantity, $maxQuantity)
                    ]);
                    $guestItem->delete();
                } else {
                    // Переносим товар на пользователя
                    $guestItem->update([
                        'user_id' => Auth::id(),
                        'session_id' => null
                    ]);
                }
            }
        });
    }
}
