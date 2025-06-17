<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Продажи';
    protected static ?string $modelLabel = 'Заказ';
    protected static ?string $pluralModelLabel = 'Заказы';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Номер заказа')
                            ->required()
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'pending' => 'В обработке',
                                'processing' => 'Обрабатывается',
                                'shipped' => 'Отправлен',
                                'delivered' => 'Доставлен',
                                'cancelled' => 'Отменен',
                            ])
                            ->required(),

                        Forms\Components\Select::make('payment_status')
                            ->label('Статус оплаты')
                            ->options([
                                'pending' => 'Ожидает оплаты',
                                'paid' => 'Оплачен',
                                'failed' => 'Ошибка оплаты',
                                'refunded' => 'Возврат',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('payment_method')
                            ->label('Способ оплаты')
                            ->required(),

                        Forms\Components\TextInput::make('transaction_id')
                            ->label('ID транзакции')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Клиент')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->nullable(),

                        Forms\Components\TextInput::make('guest_email')
                            ->label('Email гостя')
                            ->email()
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Адреса')
                    ->schema([
                        Forms\Components\Select::make('shipping_address_id')
                            ->label('Адрес доставки')
                            ->relationship('shippingAddress', 'full_address')
                            ->required(),

                        Forms\Components\Select::make('billing_address_id')
                            ->label('Адрес для счета')
                            ->relationship('billingAddress', 'full_address')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Суммы')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal_amount')
                            ->label('Подытог')
                            ->required()
                            ->numeric()
                            ->prefix('Сум'),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Скидка')
                            ->numeric()
                            ->prefix('Сум'),

                        Forms\Components\TextInput::make('shipping_amount')
                            ->label('Доставка')
                            ->numeric()
                            ->prefix('Сум'),

                        Forms\Components\TextInput::make('tax_amount')
                            ->label('Налог')
                            ->numeric()
                            ->prefix('Сум'),

                        Forms\Components\TextInput::make('total_amount')
                            ->label('Итого')
                            ->required()
                            ->numeric()
                            ->prefix('Сум'),
                    ])->columns(2),

                Forms\Components\Section::make('Бонусные баллы')
                    ->schema([
                        Forms\Components\TextInput::make('loyalty_points_earned')
                            ->label('Начислено баллов')
                            ->numeric(),

                        Forms\Components\TextInput::make('loyalty_points_spent')
                            ->label('Потрачено баллов')
                            ->numeric(),

                        Forms\Components\TextInput::make('loyalty_points_discount_amount')
                            ->label('Скидка по баллам')
                            ->numeric()
                            ->prefix('Сум'),
                    ])->columns(3),

                Forms\Components\Section::make('Примечания')
                    ->schema([
                        Forms\Components\Textarea::make('customer_notes')
                            ->label('Примечания клиента')
                            ->nullable(),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Примечания администратора')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Номер заказа')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Клиент')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'warning',
                        'shipped' => 'info',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Статус оплаты')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Сумма')
                    ->money('UZS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'В обработке',
                        'processing' => 'Обрабатывается',
                        'shipped' => 'Отправлен',
                        'delivered' => 'Доставлен',
                        'cancelled' => 'Отменен',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Статус оплаты')
                    ->options([
                        'pending' => 'Ожидает оплаты',
                        'paid' => 'Оплачен',
                        'failed' => 'Ошибка оплаты',
                        'refunded' => 'Возврат',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}