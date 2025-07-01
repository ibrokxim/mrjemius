<?php

namespace App\Services;

use App\Models\CartItem;
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
    public function getSummary(): array
    {
        $cartItems = $this->getItems();

        $subtotal = $cartItems->sum(function ($item) {
            // Сумма всех товаров
            return ($item->product->sell_price ?? $item->product->price) * $item->quantity;
        });

        $freeShippingThreshold = 500000;
        $shippingCost = 15000;

        $actualShippingCost = 0; // По умолчанию доставка бесплатная
        if ($subtotal > 0 && $subtotal < $freeShippingThreshold) {
            // Добавляем стоимость доставки, ТОЛЬКО если порог не достигнут
            $actualShippingCost = $shippingCost;
        }

        // Здесь можно добавить логику для скидок, если она появится в будущем
        $discount = 0;

        // 4. Считаем итоговую сумму с учетом доставки и скидок
        $total = $subtotal + $actualShippingCost - $discount;

        // 5. Сколько осталось до бесплатной доставки (для отображения в корзине)
        $needsForFreeShipping = max(0, $freeShippingThreshold - $subtotal);


        return [
            'subtotal' => $subtotal,
            'shipping' => $shippingCost,
            'discount' => $discount,
            'total' => $total,
            'needsForFreeShipping' => $needsForFreeShipping,
            'freeShippingThreshold' => $freeShippingThreshold,
            'items' => $cartItems,
            'count' => $cartItems->sum('quantity')
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
            // Если хотите считать количество позиций (уникальных товаров)
            // return Auth::user()->cartItems()->count();

            // Если хотите считать общее количество всех единиц товаров
            return Auth::user()->cartItems()->sum('quantity');
        }
        return 0;
    }
}
