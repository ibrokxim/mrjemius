<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function index()
    {
        $cartSummary = $this->cartService->getSummary();

        return view('cart.index', [
            'cartItems' => $cartSummary['items'],
            'subtotal' => $cartSummary['subtotal'],
            'shippingCost' => $cartSummary['shipping'],
            'total' => $cartSummary['total'],
            'freeShippingThreshold' => $cartSummary['freeShippingThreshold'],
            'needsForFreeShipping' => $cartSummary['needsForFreeShipping'],
            'baseShippingCost' => $cartSummary['baseShippingCost'],
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

        if ($product->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно товара на складе. Доступно: ' . $product->stock_quantity
            ], 400);
        }

        if (Auth::check()) {
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
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100' // Ограничиваем максимальное количество для безопасности
        ]);
        $quantity = $validated['quantity'];

        if (!$this->canAccessCartItem($cartItem)) {
            return response()->json([
                'success' => false,
                'message' => 'Нет доступа к данному элементу корзины.'
            ], 403);
        }

        if ($cartItem->product->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно товара на складе. Доступно: ' . $cartItem->product->stock_quantity
            ], 422);
        }

        $cartItem->update(['quantity' => $quantity]);

        $summary = $this->cartService->getSummary();

        $itemTotal = ($cartItem->product->sell_price ?? $cartItem->product->price) * $quantity;

        return response()->json([
            'success' => true,
            'message' => 'Количество обновлено',
            'summary' => $summary,
            'item_total_formatted' => number_format($itemTotal, 0, '.', ' '), // Отформатированная сумма для этого товара
        ]);
    }

    /**
     * Удаление товара из корзины
     */
    public function remove(CartItem $cartItem): JsonResponse
    {
        if (!$this->canAccessCartItem($cartItem)) {
            return response()->json([
                'success' => false,
                'message' => 'Нет доступа к данному элементу корзины.'
            ], 403);
        }

        $cartItem->delete();

        $summary = $this->cartService->getSummary();

        return response()->json([
            'success' => true,
            'message' => 'Товар удален из корзины',
            'summary' => $summary,
        ]);
    }

    /**
     * Очистка корзины
     */
    public function clear()
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();
        } else {
            $sessionId = Session::getId();
            CartItem::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->delete();
        }

        return redirect()->route('cart.index')->with('success', 'Корзина была успешно очищена.');
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

                if ($product->stock_quantity < 1) {
                    $skippedCount++;
                    continue;
                }

                $existingCartItem = CartItem::where('user_id', Auth::id())
                    ->where('product_id', $product->id)
                    ->first();

                if ($existingCartItem) {
                    if ($existingCartItem->quantity < $product->stock_quantity) {
                        $existingCartItem->increment('quantity');
                        $addedCount++;
                    } else {
                        $skippedCount++;
                    }
                } else {
                    CartItem::create([
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'quantity' => 1
                    ]);
                    $addedCount++;
                }

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
    public function setDeliveryMethod(Request $request)
    {
        $validated = $request->validate([
            'delivery_method' => 'required|string|in:delivery,pickup',
        ]);
        session(['delivery_method' => $validated['delivery_method']]);

        return response()->json(['success' => true]);
    }
}
