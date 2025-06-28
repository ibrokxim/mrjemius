<?php

namespace App\Http\Controllers;


use App\Events\OrderPlaced;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Показать страницу оформления заказа
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = $this->cartService->getItems();
        $cartSummary = $this->cartService->getSummary(); // Метод должен возвращать subtotal, total и т.д.

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Ваша корзина пуста.');
        }

        // Получаем адреса пользователя
        $addresses = $user->addresses()->get();

        return view('pages.checkout', [
            'cartItems' => $cartItems,
            'cartSummary' => $cartSummary,
            'addresses' => $addresses,
        ]);
    }

    /**
     * Обработать и сохранить заказ
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $cartItems = $this->cartService->getItems();
        $cartSummary = $this->cartService->getSummary();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Ваша корзина пуста.');
        }

        // ВАЛИДАЦИЯ ДАННЫХ ИЗ ФОРМЫ
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'customer_notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|string|in:cash,card_online', // Пример
        ]);

        // Используем транзакцию, чтобы все операции были выполнены успешно или ни одной
        DB::beginTransaction();
        try {
            // 1. Создать или обновить адрес доставки
            $shippingAddress = Address::create([
                'user_id' => $user->id,
                'type' => 'shipping',
                'full_name' => $validated['full_name'],
                'phone_number' => $validated['phone_number'],
                'address_line_1' => $validated['address_line_1'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'country_code' => 'UZ', // Пример
            ]);

            // 2. Создать заказ
            $order = Order::create([
                'order_number' => 'ORD-' . time() . '-' . $user->id, // Уникальный номер заказа
                'user_id' => $user->id,
                'shipping_address_id' => $shippingAddress->id,
                'status' => 'pending', // Начальный статус
                'subtotal_amount' => $cartSummary['subtotal'],
                'total_amount' => $cartSummary['total'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'customer_notes' => $validated['customer_notes'],
            ]);

            // 3. Добавить товары в заказ (Order Items)
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price_at_purchase' => $item->product->sell_price ?? $item->product->price,
                    'total_price' => ($item->product->sell_price ?? $item->product->price) * $item->quantity,
                ]);

                // 4. Уменьшить количество товара на складе
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // 5. Очистить корзину
            $this->cartService->clear();

            DB::commit();
            \Log::info("Заказ №{$order->id} создан. Сейчас будет вызвано событие OrderPlaced.");
            event(new OrderPlaced($order));
            \Log::info("Событие OrderPlaced для заказа №{$order->id} было вызвано.");
            // Перенаправить на страницу успеха
            return redirect()->route('order.success')->with('success', 'Ваш заказ успешно оформлен!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Записать ошибку в лог
            \Log::error('Ошибка оформления заказа: ' . $e->getMessage());
            return back()->with('error', 'Произошла ошибка при оформлении заказа. Пожалуйста, попробуйте снова.');
        }
    }

}
