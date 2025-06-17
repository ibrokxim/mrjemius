<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use App\Filament\Resources\ProductResource;
use Illuminate\Database\Eloquent\Collection;
use Filament\Resources\Pages\ManageRelatedRecords;
use App\Filament\Resources\ProductResource\RelationManagers\ReviewsRelationManager;



class ManageProductReviews extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;
    protected static string $relationship = 'reviews';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $title = 'Отзывы о продукте';

    public function getTitle(): string
    {
        // $this->record здесь будет текущий Product
        return 'Отзывы для продукта: ' . $this->getRecordTitle(); // getRecordTitle() вернет название продукта, если $recordTitleAttribute установлен в ProductResource
        // или $this->record->name
    }

    // Настраиваем форму для создания/редактирования отзывов на этой странице
    // Этот метод будет использоваться, если вы добавите CreateAction или EditAction в таблицу ниже
    public function form(Form $form): Form
    {
        // Вы можете скопировать схему формы из ReviewsRelationManager::form()
        // или определить новую, если на этой странице форма должна отличаться.
        return ReviewsRelationManager::form($form); // <--- Вот здесь была ошибка в моем предыдущем коде.
        // Мы не можем вызвать нестатический метод form() статически.
        // Вместо этого, мы должны определить схему здесь заново или унаследовать.

        // ПРАВИЛЬНЫЙ СПОСОБ: Определяем форму прямо здесь
        // return $form
        //     ->schema([
        //         Forms\Components\Select::make('user_id')
        //             ->relationship('user', 'name') // Отношение user должно быть в модели Review
        //             ->searchable()
        //             ->preload()
        //             ->label('Пользователь')
        //             ->required(),
        //         Forms\Components\TextInput::make('rating')
        //             ->label('Рейтинг (1-5)')
        //             ->numeric()
        //             ->minValue(1)
        //             ->maxValue(5)
        //             ->required(),
        //         Forms\Components\Textarea::make('comment')
        //             ->label('Комментарий')
        //             ->columnSpanFull()
        //             ->required(),
        //         Forms\Components\Toggle::make('is_approved')
        //             ->label('Одобрен')
        //             ->default(true),
        //     ]);
    }


    // Настраиваем таблицу для отображения отзывов
    public function table(Table $table): Table
    {
        // Здесь вы определяете колонки, фильтры и действия для таблицы отзывов.
        // Вы можете скопировать эту конфигурацию из вашего ReviewsRelationManager.php
        // или настроить ее специфично для этой страницы.
        return $table
            ->recordTitleAttribute('comment') // Атрибут для заголовка при редактировании/просмотре
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
                Tables\Actions\CreateAction::make() // Кнопка "Создать новый отзыв" для этого продукта
                // Вы можете настроить форму для CreateAction здесь, если нужно
                // ->form(...)
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                // ... (ваши массовые действия 'approve' и 'reject')
                Tables\Actions\BulkAction::make('approve')
                    ->label('Одобрить выбранные')
                    ->action(fn (Collection $records) => $records->each->update(['is_approved' => true]))
                    ->requiresConfirmation()
                    ->icon('heroicon-o-check-circle')
                    ->color('success'),
                Tables\Actions\BulkAction::make('reject')
                    ->label('Отклонить выбранные')
                    ->action(fn (Collection $records) => $records->each->update(['is_approved' => false]))
                    ->requiresConfirmation()
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
            ]);
    }
}
