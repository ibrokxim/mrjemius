<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Order;
use App\Models\User;
use App\Models\Product; // Предполагается, что у вас есть эти модели

class StoreStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1; // Порядок виджета на дашборде (меньше = выше)

    protected function getCards(): array
    {
        return [
            Card::make('Заказы сегодня', Order::whereDate('created_at', today())->count())
                ->description('Новых заказов за день')
                ->descriptionIcon('heroicon-m-shopping-cart') // Используйте иконки Heroicons v2
                ->color('success'),

            Card::make('Всего продуктов', Product::count())
                ->description('Активных: ' . Product::where('is_active', true)->count())
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('info'),

            Card::make('Новые пользователи (неделя)', User::where('created_at', '>=', now()->subWeek())->count())
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}
