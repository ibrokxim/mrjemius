<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getItems(): Collection
    {
        if (Auth::check()) {
            return Auth::user()->cartItems()->with('product.primaryImage')->get();
        }

        return collect();
    }

    /**
     * Добавить товар в корзину.
     *
     * @param int $productId
     * @param int $quantity
     * @return CartItem
     */
    public function add(int $productId, int $quantity = 1): CartItem
    {
        $user = Auth::user();
        if (!$user) {
            // Обработка для неавторизованных пользователей (можно бросить исключение или вернуть null)
            abort(403, 'Только авторизованные пользователи могут добавлять товары в корзину.');
        }

        // Проверяем, есть ли уже такой товар в корзине
        $cartItem = $user->cartItems()->where('product_id', $productId)->first();

        if ($cartItem) {
            // Если есть, просто обновляем количество
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Если нет, создаем новую запись
            $cartItem = CartItem::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return $cartItem;
    }

    /**
     * Обновить количество товара в корзине.
     *
     * @param int $cartItemId
     * @param int $quantity
     * @return CartItem|null
     */
    public function update(int $cartItemId, int $quantity): ?CartItem
    {
        $cartItem = Auth::user()->cartItems()->find($cartItemId);

        if ($cartItem) {
            if ($quantity > 0) {
                // Проверка на наличие на складе
                if ($quantity > $cartItem->product->stock_quantity) {
                    abort(400, 'Запрошенное количество превышает остаток на складе.');
                }
                $cartItem->quantity = $quantity;
                $cartItem->save();
            } else {
                // Если количество 0 или меньше, удаляем товар
                $this->remove($cartItemId);
                return null;
            }
        }
        return $cartItem;
    }

    /**
     * Удалить товар из корзины.
     *
     * @param int $cartItemId
     * @return bool
     */
    public function remove(int $cartItemId): bool
    {
        $cartItem = Auth::user()->cartItems()->find($cartItemId);

        if ($cartItem) {
            return $cartItem->delete();
        }

        return false;
    }

    /**
     * Полностью очистить корзину пользователя.
     */
    public function clear(): void
    {
        Auth::user()->cartItems()->delete();
    }

    /**
     * Получить итоговые суммы по корзине.
     *
     * @return array
     */
    public function getSummary(string $deliveryMethod = 'delivery'): array
    {
        $cartItems = $this->getItems();
        $subtotal = $cartItems->sum(fn($item) => ($item->product->sell_price ?? $item->product->price) * $item->quantity);

        $freeShippingThreshold = config('cart.free_shipping_threshold', 500000);
        $baseShippingCost = config('cart.shipping_cost', 20000);

        //$actualShippingCost = ($subtotal > 0 && $subtotal < $freeShippingThreshold) ? $baseShippingCost : 0;
        $displayShippingCost = ($subtotal >= $freeShippingThreshold || $subtotal == 0) ? 0 : $baseShippingCost;
        $total = $subtotal;
        $needsForFreeShipping = max(0, $freeShippingThreshold - $subtotal);

        return [
            'items' => $cartItems,
            'count' => $cartItems->sum('quantity'),
            'subtotal' => $subtotal,
            'shipping' => $displayShippingCost,
            'baseShippingCost' => $baseShippingCost, // Оставляем на всякий случай
            'total' => $total,
            'needsForFreeShipping' => $needsForFreeShipping,
            'freeShippingThreshold' => $freeShippingThreshold,
        ];
    }

    /**
     * Получить общее количество уникальных товаров или всех единиц в корзине.
     *
     * @return int
     */
    public function getCount(): int
    {
        if (Auth::check()) {
            return Auth::user()->cartItems()->sum('quantity');
        }
        return 0;
    }

    public function itemExists(int $productId, int $userId): bool
    {
        return CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }
    public function findItem(int $cartItemId): ?CartItem
    {
        if (!Auth::check()) return null;
        return Auth::user()->cartItems()->find($cartItemId);
    }
}
