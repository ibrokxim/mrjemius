<?php

namespace App\DTO;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateOrderDTO
{
    public function __construct(
        public readonly ?int    $userId,
        public readonly string  $customerName,
        public readonly string  $customerEmail,
        public readonly string  $customerPhone,
        public readonly string  $shippingAddress,
        public readonly string  $paymentMethod,
        public readonly array   $items, // Массив позиций корзины
        public readonly ?string $customerNotes = null
    )
    {
    }

    public static function fromRequest(Request $request): self
    {
        $validated = $request->validated();

        $cartService = app(CartService::class);
        $cartItems = $cartService->get()->map(fn($item) => [
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
        ])->toArray();

        return new self(
            userId: Auth::id(), // Может быть null для гостей
            customerName: $validated['customer_name'],
            customerEmail: $validated['customer_email'],
            customerPhone: $validated['customer_phone'],
            shippingAddress: $validated['shipping_address'],
            paymentMethod: $validated['payment_method'],
            items: $cartItems,
            customerNotes: $validated['customer_notes'] ?? null
        );
    }
}
