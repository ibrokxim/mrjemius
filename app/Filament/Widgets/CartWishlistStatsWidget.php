<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\CartItem;
use App\Models\WishlistItem;
use Illuminate\Support\Facades\DB;

class CartWishlistStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Общая стоимость товаров в корзинах
        $cartTotal = CartItem::join('products', 'cart_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(cart_items.quantity * COALESCE(products.sell_price, products.price)) as total')
            ->value('total') ?? 0;

        // Уникальные пользователи с товарами в корзине
        $usersWithCarts = CartItem::distinct('user_id')->whereNotNull('user_id')->count();

        // Наиболее популярный товар в избранном
        $popularWishlistProduct = WishlistItem::select('product_id')
            ->groupBy('product_id')
            ->orderByRaw('COUNT(*) DESC')
            ->with('product')
            ->first();

        return [
            Stat::make('Товары в корзинах', CartItem::count())
                ->description('Всего товаров в корзинах пользователей')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('primary'),

            Stat::make('Стоимость корзин', number_format($cartTotal, 0, ',', ' ') . ' сум')
                ->description('Общая стоимость всех товаров в корзинах')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Пользователи с корзинами', $usersWithCarts)
                ->description('Количество пользователей с товарами в корзине')
                ->descriptionIcon('heroicon-o-users')
                ->color('warning'),

            Stat::make('Товары в избранном', WishlistItem::count())
                ->description('Всего товаров в избранном у пользователей')
                ->descriptionIcon('heroicon-o-heart')
                ->color('danger'),

            Stat::make('Уникальные избранные', WishlistItem::distinct('product_id')->count())
                ->description('Количество уникальных товаров в избранном')
                ->descriptionIcon('heroicon-o-star')
                ->color('info'),

            Stat::make('Популярный товар', $popularWishlistProduct?->product?->name ?? 'Нет данных')
                ->description('Самый популярный товар в избранном')
                ->descriptionIcon('heroicon-o-fire')
                ->color('gray'),
        ];
    }
}
