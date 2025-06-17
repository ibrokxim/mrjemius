<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionResource\Pages;
use App\Models\Promotion;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('description')
                    ->maxLength(65535),
                Select::make('type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed_amount' => 'Fixed Amount',
                        'free_shipping' => 'Free Shipping',
                    ])
                    ->required(),
                TextInput::make('value')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(fn (string $context) => $context === 'percentage' ? 100 : null),
                TextInput::make('max_uses')
                    ->numeric()
                    ->minValue(0)
                    ->nullable(),
                TextInput::make('max_uses_user')
                    ->numeric()
                    ->minValue(0)
                    ->nullable(),
                TextInput::make('minimum_spend')
                    ->numeric()
                    ->minValue(0)
                    ->nullable(),
                DateTimePicker::make('starts_at')
                    ->nullable(),
                DateTimePicker::make('expires_at')
                    ->nullable(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'info',
                        'fixed_amount' => 'success',
                        'free_shipping' => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('value')
                    ->money(fn (Promotion $record) => $record->type === 'fixed_amount' ? 'USD' : null)
                    ->suffix(fn (Promotion $record) => $record->type === 'percentage' ? '%' : '')
                    ->sortable(),
                TextColumn::make('uses_count')
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}