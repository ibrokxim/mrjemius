<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Form; // <--- Правильный импорт для метода form
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Table; // <--- Правильный импорт для метода table
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';
    protected static ?string $recordTitleAttribute = 'comment';
    protected static ?string $modelLabel = 'Отзыв';
    protected static ?string $pluralModelLabel = 'Отзывы';

    // Убираем 'static' из объявления метода form
    public function form(Form $form): Form // <--- ИЗМЕНЕНИЕ ЗДЕСЬ
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Пользователь')
                    ->required(),
                TextInput::make('rating')
                    ->label('Рейтинг (1-5)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required(),
                Textarea::make('comment')
                    ->label('Комментарий')
                    ->columnSpanFull()
                    ->required(),
                Toggle::make('is_approved')
                    ->label('Одобрен')
                    ->default(true),
            ]);
    }

    // Убираем 'static' из объявления метода table
    public function table(Table $table): Table // <--- ИЗМЕНЕНИЕ ЗДЕСЬ
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rating')
                    ->label('Рейтинг')
                    ->sortable(),
                TextColumn::make('comment')
                    ->label('Комментарий')
                    ->limit(50)
                    ->wrap()
                    ->searchable(),
                BooleanColumn::make('is_approved')
                    ->label('Одобрен')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Одобрен'),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('approve')
                    ->label('Одобрить выбранные')
                    ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_approved' => true]))
                    ->requiresConfirmation()
                    ->icon('heroicon-o-check-circle')
                    ->color('success'),
                Tables\Actions\BulkAction::make('reject')
                    ->label('Отклонить выбранные')
                    ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_approved' => false]))
                    ->requiresConfirmation()
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
            ]);
    }
}
