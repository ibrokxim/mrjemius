<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [];
    }
//        $currentMonth = Carbon::now()->startOfMonth();
//        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
//
//        $currentMonthUsers = User::where('created_at', '>=', $currentMonth)->count();
//        $lastMonthUsers = User::where('created_at', '>=', $lastMonth)
//            ->where('created_at', '<', $currentMonth)
//            ->count();
//
//        $currentMonthOrders = Order::where('created_at', '>=', $currentMonth)->count();
//        $lastMonthOrders = Order::where('created_at', '>=', $lastMonth)
//            ->where('created_at', '<', $currentMonth)
//            ->count();
//
//        $totalRevenue = Order::sum('total_price');
//        $averageOrderValue = Order::avg('total_price');
//
//        return [
//            Card::make('New Users This Month', $currentMonthUsers)
//                ->description(($currentMonthUsers > $lastMonthUsers ? '+' : '-') . abs($currentMonthUsers - $lastMonthUsers) . ' from last month')
//                ->descriptionIcon($currentMonthUsers > $lastMonthUsers ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
//                ->color($currentMonthUsers > $lastMonthUsers ? 'success' : 'danger'),
//
//            Card::make('Orders This Month', $currentMonthOrders)
//                ->description(($currentMonthOrders > $lastMonthOrders ? '+' : '-') . abs($currentMonthOrders - $lastMonthOrders) . ' from last month')
//                ->descriptionIcon($currentMonthOrders > $lastMonthOrders ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
//                ->color($currentMonthOrders > $lastMonthOrders ? 'success' : 'danger'),
//
//            Card::make('Total Revenue', number_format($totalRevenue, 2) . ' USD')
//                ->description('Average order: ' . number_format($averageOrderValue, 2) . ' USD')
//                ->color('success'),
//        ];
//    }
}
