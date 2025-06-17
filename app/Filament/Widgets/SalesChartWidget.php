<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Продажи за последние 30 дней';
    protected static ?int $sort = 2; // Порядок после карточек статистики

    protected function getData(): array
    {
        $salesData = Trend::model(Order::class) // Используем модель Order
        ->between(
            start: now()->subDays(29), // За последние 30 дней (включая сегодня)
            end: now(),
        )
            ->perDay() // Группируем по дням
            ->sum('total_amount'); // Суммируем по полю total_amount

        return [
            'datasets' => [
                [
                    'label' => 'Сумма продаж (Сум)',
                    'data' => $salesData->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3b82f6', // Синий
                ],
            ],
            'labels' => $salesData->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('d M')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
