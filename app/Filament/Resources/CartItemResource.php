<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CartItemResource\Pages;
use App\Models\CartItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;

class CartItemResource extends Resource
{
    protected static ?string $model = CartItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Продажи';
    protected static ?string $modelLabel = 'Корзина';
    protected static ?string $pluralModelLabel = 'Корзины';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('product_id')
                    ->label('Товар')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('quantity')
                    ->label('Количество')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1),

                Forms\Components\TextInput::make('session_id')
                    ->label('ID сессии')
                    ->maxLength(100)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('product.primaryImage.image_url')
                    ->label('Изображение')
                    ->circular()
                    ->defaultImageUrl(url('/assets/images/products/product-img-1.jpg'))
                    ->size(50),

                TextColumn::make('product.name')
                    ->label('Товар')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->limit(30),

                TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->searchable()
                    ->sortable()
                    ->default('Гость')
                    ->badge()
                    ->color(fn ($record) => $record->user ? 'success' : 'gray'),

                TextColumn::make('quantity')
                    ->label('Количество')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('product.price')
                    ->label('Цена за единицу')
                    ->money('UZS')
                    ->sortable(),

                TextColumn::make('total_price')
                    ->label('Общая стоимость')
                    ->money('UZS')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                TextColumn::make('session_id')
                    ->label('Сессия')
                    ->limit(10)
                    ->tooltip(fn ($record) => $record->session_id)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Добавлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('product.category_id')
                    ->label('Категория товара')
                    ->relationship('product.category', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('guest_only')
                    ->label('Только гости')
                    ->query(fn (Builder $query): Builder => $query->whereNull('user_id')),

                Filter::make('registered_only')
                    ->label('Только зарегистрированные')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('user_id')),

                Filter::make('created_today')
                    ->label('Добавлено сегодня')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Просмотр'),
                Tables\Actions\EditAction::make()
                    ->label('Редактировать'),
                Tables\Actions\DeleteAction::make()
                    ->label('Удалить'),
            ])
            ->bulkActions([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранные'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить в корзину'),
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
            'index' => Pages\ListCartItems::route('/'),
            'create' => Pages\CreateCartItem::route('/create'),
            'edit' => Pages\EditCartItem::route('/{record}/edit'),
        ];
    }
}
