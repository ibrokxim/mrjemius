<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Магазин';

    protected static ?string $navigationLabel = 'Транзакции';
    protected static ?string $pluralModelLabel = 'Транзакции';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('order_id')
                    ->label('Номер заказа')
                    ->content(fn ($record) => $record?->order?->order_id ?? 'Не привязан'),

                Placeholder::make('paycom_transaction_id')
                    ->label('ID транзакции в Payme'),


                Placeholder::make('amount')
                    ->label('Сумма')
                    ->content(fn ($record) => number_format($record->amount / 100, 2, '.', ' ') . ' UZS'), // Конвертируем в сумы для отображения

                Placeholder::make('state')
                    ->label('Статус')
                    ->content(fn ($record) => match ($record->state) {
                        1 => 'Создана (в ожидании оплаты)',
                        2 => 'Успешно завершена',
                        -1 => 'Отменена',
                        -2 => 'Отменена после завершения (возврат)',
                        default => 'Неизвестный статус',
                    }),

                Placeholder::make('reason')
                    ->label('Причина отмены (код)')
                    ->content(fn ($record) => $record->reason ?? '-'), // Выводим код причины

                Placeholder::make('paycom_time_datetime')
                    ->label('Дата и время создания (Payme)')
                    ->content(fn ($record) => $record->paycom_time_datetime?->format('d.m.Y H:i:s')),

                Placeholder::make('cancel_time')
                    ->label('Дата и время отмены')
                    ->content(fn ($record) => $record->cancel_time?->format('d.m.Y H:i:s') ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ID заказа (предполагаем, что связь называется `order`)
                TextColumn::make('order_id')
                    ->label('Заказ')
                    ->searchable()
                    ->sortable(),

                // ID транзакции в Payme
                TextColumn::make('paycom_transaction_id')
                    ->label('ID в Payme')
                    ->searchable(),

                // Сумма
                TextColumn::make('amount')
                    ->label('Сумма (в тийинах)')
                    ->numeric() // Простое числовое форматирование
                    ->sortable()
                    ->tooltip('Сумма указана в тийинах'),

                // Статус (state)
                BadgeColumn::make('state')
                    ->label('Статус')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        1 => 'Создана',
                        2 => 'Завершена',
                        -1 => 'Отменена',
                        -2 => 'Отменена',
                        default => 'В ожидании'
                    })
                    ->colors([
                        'primary' => 1,
                        'success' => 2,
                        'danger' => fn ($state) => in_array($state, [-1, -2]),
                        'warning' => fn ($state) => is_null($state),
                    ]),

                // Дата создания транзакции
                TextColumn::make('paycom_time_datetime')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                // Дата отмены транзакции
                TextColumn::make('cancel_time')
                    ->label('Дата отмены')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // По умолчанию скрыта
            ])
            ->filters([
                SelectFilter::make('state')
                    ->label('Статус')
                    ->options([
                        1 => 'Создана',
                        2 => 'Завершена',
                        -1 => 'Отменена',
                        -2 => 'Отменена (после завершения)',
                    ]),
                Filter::make('paycom_time_datetime')
                    ->form([
                        DatePicker::make('created_from')->label('От'),
                        DatePicker::make('created_until')->label('До'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn (Builder $query, $date): Builder => $query->whereDate('paycom_time_datetime', '>=', $date))
                            ->when($data['created_until'], fn (Builder $query, $date): Builder => $query->whereDate('paycom_time_datetime', '<=', $date));
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('paycom_time_datetime', 'desc'); // Сортировка по умолчанию
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
