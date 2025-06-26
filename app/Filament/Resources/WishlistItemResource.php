<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WishlistItemResource\Pages;
use App\Models\WishlistItem;
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

class WishlistItemResource extends Resource
{
    protected static ?string $model = WishlistItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Продажи';
    protected static ?string $modelLabel = 'Избранное';
    protected static ?string $pluralModelLabel = 'Избранные';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('product_id')
                    ->label('Товар')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->required(),
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
                    ->badge()
                    ->color('success'),

                TextColumn::make('product.category.name')
                    ->label('Категория')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('product.price')
                    ->label('Цена')
                    ->money('UZS')
                    ->sortable(),

                TextColumn::make('product.sell_price')
                    ->label('Цена со скидкой')
                    ->money('UZS')
                    ->sortable()
                    ->default('—')
                    ->color('danger'),

                TextColumn::make('product.stock_quantity')
                    ->label('Остаток')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        optional($record->product)->stock_quantity > 10 => 'success',
                        optional($record->product)->stock_quantity > 0 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('created_at')
                    ->label('Добавлено в избранное')
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

                Filter::make('created_today')
                    ->label('Добавлено сегодня')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today())),

                Filter::make('products_in_stock')
                    ->label('Товары в наличии')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereHas('product', fn ($q) => $q->where('stock_quantity', '>', 0))
                    ),

                Filter::make('products_on_sale')
                    ->label('Товары со скидкой')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereHas('product', fn ($q) => $q->whereNotNull('sell_price'))
                    ),
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
                    ->label('Добавить в избранное'),
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
            'index' => Pages\ListWishlistItems::route('/'),
            'create' => Pages\CreateWishlistItem::route('/create'),
            'edit' => Pages\EditWishlistItem::route('/{record}/edit'),
        ];
    }
}
