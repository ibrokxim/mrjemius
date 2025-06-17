<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;
use App\Filament\Resources\OrderResource; // Для ссылок

class RecentOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 3; // Порядок после графика
    protected int | string | array $columnSpan = 'full'; // Занять всю ширину

    protected static ?string $heading = 'Последние 10 заказов';

    protected function getTableQuery(): Builder
    {
        return Order::query()->with('user')->latest()->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('order_number')
                ->label('Номер заказа')
                ->url(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record])), // Ссылка на редактирование заказа
            TextColumn::make('user.name')->label('Клиент')->placeholder('Гость'), // Отобразит имя пользователя или "Гость"
            TextColumn::make('status')->label('Статус')->badge()
                ->colors([ // Пример цветов для статусов
                    'primary' => 'processing',
                    'warning' => 'pending',
                    'success' => 'delivered',
                    'danger' => 'cancelled',
                ]),
            TextColumn::make('total_amount')->money('sum')->label('Сумма'),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false; // Отключаем пагинацию для этого виджета
    }
}
