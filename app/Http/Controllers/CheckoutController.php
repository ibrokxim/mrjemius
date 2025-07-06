<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Address;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Services\TelegramService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $telegramService;

    public function __construct(CartService $cartService, TelegramService $telegramService)
    {
        $this->cartService = $cartService;
        $this->telegramService = $telegramService;
    }

    public function index()
    {
        $user = Auth::user();
        $addresses = $user->addresses()->get();
        $cartItems = $this->cartService->getItems();
        $cartSummary = $this->cartService->getSummary(); // Метод должен возвращать subtotal, total и т.д.

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Ваша корзина пуста.');
        }

        $latestAddress = $user->addresses()->latest()->first();

        $deliveryDates = [
            'today' => now()->format('Y-m-d'),
            'tomorrow' => now()->addDay()->format('Y-m-d'),
            'day_after' => now()->addDays(2)->format('Y-m-d'),
        ];

        $addresses = $user->addresses()->get();
        $deliveryMethod = session('delivery_method', 'delivery');
        return view('pages.checkout', [
            'cartItems' => $cartItems,
            'cartSummary' => $cartSummary,
            'addresses' => $addresses,
            'deliveryMethod' => $deliveryMethod,
            'deliveryDates' => $deliveryDates,
            'latestAddress' => $latestAddress,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $cartItems = $this->cartService->getItems();
        $cartSummary = $this->cartService->getSummary();

        if ($cartItems->isEmpty()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Ваша корзина пуста.'], 400);
            }
            return back()->with('error', 'Ваша корзина пуста.');
        }

        // ИСПРАВЛЕНИЕ: Возвращаем full_name и phone_number в валидацию
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'delivery_method' => 'required|string|in:delivery,pickup',
            'address_line_1' => 'required_if:address_option,new|nullable|string|max:255',
            'city' => 'required_if:address_option,new|nullable|string|max:100',
            'payment_method' => 'required|string|in:cash,card_online',
            'customer_notes' => 'nullable|string|max:1000',
            'address_option' => 'required_if:delivery_method,delivery|string',
            'delivered_at' => 'required|date_format:Y-m-d',
            // Поля нового адреса обязательны, ТОЛЬКО если выбрана опция "new"

        ]);

        $order = null;
        DB::beginTransaction();
        try {
            $shippingAddressId = null;

            if ($validated['delivery_method'] === 'delivery') {

                // 2. УСЛОВНАЯ ЛОГИКА: СОЗДАЕМ АДРЕС ИЛИ ИСПОЛЬЗУЕМ СУЩЕСТВУЮЩИЙ
                if ($validated['address_option'] === 'new') {
                    // Пользователь выбрал "Добавить новый адрес"
                    $newAddress = Address::create([
                        'user_id' => $user->id,
                        'type' => 'shipping',
                        'full_name' => $validated['full_name'],
                        'phone_number' => $validated['phone_number'],
                        'address_line_1' => $validated['address_line_1'],
                        'city' => $validated['city'],
                        'postal_code' => '000000', // Можно сделать необязательным
                        'country_code' => 'UZ',
                    ]);
                    $shippingAddressId = $newAddress->id;
                } else {
                    // Пользователь выбрал существующий адрес. ID адреса находится в 'address_option'
                    $addressId = $validated['address_option'];

                    // Важная проверка безопасности: убеждаемся, что выбранный адрес принадлежит текущему пользователю
                    $address = $user->addresses()->findOrFail($addressId);
                    $shippingAddressId = $address->id;
                }
            }

            // 3. Создаем заказ, используя полученный ID адреса
            $order = Order::create([
                'order_number' => 'ORD-' . time() . '-' . $user->id,
                'user_id' => $user->id,
                'shipping_address_id' => $shippingAddressId, // <-- Используем ID
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal_amount' => $cartSummary['subtotal'],
                'shipping_amount' => $cartSummary['shipping'] ?? 0,
                'total_amount' => $cartSummary['total'],
                'shipping_method' => $validated['delivery_method'],
                'payment_method' => $validated['payment_method'],
                'customer_notes' => $validated['customer_notes'],
                'delivered_at' => $validated['delivered_at'],
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price_at_purchase' => $item->product->sell_price ?? $item->product->price,
                    'total_price' => ($item->product->sell_price ?? $item->product->price) * $item->quantity,
                ]);
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при создании заказа: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Не удалось создать заказ. Попробуйте снова.'], 500);
            }
            return back()->with('error', 'Произошла ошибка при создании заказа.');
        }
        $this->cartService->clear();
        if ($validated['payment_method'] === 'card_online') {
            return response()->json([
                'success' => true,
                'amount' => $order->total_amount * 100 ,
                'order_id' => $order->id,
                'user_id' => $order->user_id
            ]);
        } else {
            // ОПЛАТА НАЛИЧНЫМИ: Обновляем статус, отправляем уведомления и чистим корзину
            $order->update(['status' => 'processing']);

            $this->telegramService->sendOrderNotifications($order);

            $this->cartService->clear();

            return redirect()->route('order.success')->with([
                'success' => __('success'),
                'order_number' => $order->order_number
            ]);
        }
    }

    public function success()
    {
        return view('pages.order_success');
    }

}
