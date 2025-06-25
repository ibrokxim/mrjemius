<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\ReviewsRelationManager;

class ProductResource extends Resource
{
    use Translatable;
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Каталог'; // Группа в навигации
    protected static ?string $modelLabel = 'Продукт'; // Название модели в единственном числе
    protected static ?string $pluralModelLabel = 'Продукты'; // Название модели во множественном числе
    protected static ?int $navigationSort = 1;

    public static function getRelations(): array
    {
        return [ReviewsRelationManager::class,];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Основная информация и SEO')
                    ->tabs([
                        Tabs\Tab::make('Основное')
                            ->schema([
                                Section::make('Главное')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Название')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true) // Обновлять slug при потере фокуса с поля name
                                            ->afterStateUpdated(fn (callable $set, ?string $state) => $set('slug', Str::slug($state))), // Автогенерация slug

                                        TextInput::make('slug')
                                            ->label('Slug (ЧПУ)')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(Product::class, 'slug', ignoreRecord: true), // Проверка уникальности, игнорируя текущую запись при редактировании

                                        Select::make('category_id')
                                            ->label('Категория')
                                            ->relationship('category', 'name') // Загружает категории для выбора
                                            ->searchable()
                                            ->preload(), // Предзагрузка опций для лучшего UX

                                        TextInput::make('sku')
                                            ->label('Артикул (SKU)')
                                            ->maxLength(100)
                                            ->unique(Product::class, 'sku', ignoreRecord: true)
                                            ->nullable(),
                                    ]),

                                Section::make('Описание и Цены')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\RichEditor::make('description') // Текстовый редактор
                                        ->label('Полное описание')
                                            ->columnSpanFull() // На всю ширину
                                            ->nullable(),

                                        Textarea::make('short_description')
                                            ->label('Состав')
                                            ->maxLength(500)
                                            ->columnSpanFull()
                                            ->nullable(),

                                        TextInput::make('price')
                                            ->label('Цена')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('Сум'), // Или ваша валюта

                                        TextInput::make('sell_price')
                                            ->label('Цена со скидкой')
                                            ->numeric()
                                            ->minValue(0)
                                            ->lte('price') // Должна быть меньше или равна основной цене
                                            ->nullable()
                                            ->prefix('Сум'),
                                    ]),

                                Section::make('Остатки и Параметры')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('stock_quantity')
                                            ->label('Количество на складе')
                                            ->required()
                                            ->integer()
                                            ->minValue(0),

                                        TextInput::make('weight_kg')
                                            ->label('Вес (кг)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->nullable(),

                                        Toggle::make('is_active')
                                            ->label('Активен')
                                            ->default(true),

                                        Toggle::make('is_featured')
                                            ->label('Рекомендуемый'),
                                    ]),

                                KeyValue::make('attributes') // Для JSON поля attributes
                                ->label('Дополнительные атрибуты')
                                    ->keyLabel('Название атрибута')
                                    ->valueLabel('Значение атрибута')
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Изображения')
                            ->schema([
                                Repeater::make('images') // Используем репитер для hasMany связи с ProductImage
                                ->label('Изображения товара')
                                    ->relationship() // Указывает, что это связь (Filament попытается определить ее)
                                    ->schema([
                                        FileUpload::make('image_url') // Если image_url - это путь к файлу
                                        ->label('Файл изображения')
                                            ->image() // Указывает, что это изображение (для превью)
                                            ->acceptedFileTypes(['jpeg', 'png', 'gif', 'webp'])
                                            ->directory('product-images') // Директория для загрузки в storage/app/public
                                            ->visibility('public')
                                            ->nullable(),

                                        TextInput::make('alt_text')
                                            ->label('Alt текст')
                                            ->maxLength(255),
                                        Toggle::make('is_primary')
                                            ->label('Основное изображение'),
                                        TextInput::make('sort_order')
                                            ->label('Порядок сортировки')
                                            ->integer()
                                            ->default(0),
                                    ])
                                    ->defaultItems(1) // По умолчанию одно поле для изображения
                                    ->columnSpanFull()
                                    ->grid(3) // Расположить поля изображения в гриде
                                    ->reorderable() // Позволить менять порядок
                                    ->collapsible(), // Сделать сворачиваемым
                            ]),

                        Tabs\Tab::make('Теги')
                            ->schema([
                                Select::make('tags')
                                    ->label('Теги')
                                    ->multiple()
                                    ->relationship('tags', 'name') // Связь belongsToMany
                                    ->preload() // Предзагрузка для лучшего UX при небольшом кол-ве тегов
                                    ->searchable(),
                            ]),

                        Tabs\Tab::make('SEO')
                            ->schema([
                                Section::make('SEO Настройки')
                                    ->schema([
                                    TextInput::make('meta_title')->label('Meta Title'),
                                    Textarea::make('meta_description')->label('Meta Description'),
                                    Textarea::make('meta_keywords')->label('Meta Keywords'),
                                    TextInput::make('og_title')->label('OpenGraph Title'),
                                    Textarea::make('og_description')->label('OpenGraph Description'),
                                    FileUpload::make('og_image_url') // Если загружаем OG Image
                                    ->label('OpenGraph Image')
                                        ->image()
                                        ->directory('seo-og-images')
                                        ->visibility('public')
                                        ->nullable(),
                                    // TextInput::make('og_image_url')->label('OpenGraph Image URL')->url()->nullable(), // Если URL
                                    TextInput::make('canonical_url')->label('Канонический URL')->url()->nullable(),
                                    TextInput::make('robots_tags')->label('Robots Tag'),
                                    Textarea::make('custom_html_head_start')->label('HTML в начало <head>'),
                                    Textarea::make('custom_html_head_end')->label('HTML в конец <head>'),
                                ])->columns(1)
                        ]),
                    ])->columnSpanFull(), // Вкладки на всю ширину
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primaryImage.image_url') // Отображение основного изображения
                ->label('Фото')
                    ->defaultImageUrl(url('/placeholder.jpg')), // Заглушка, если фото нет

                TextColumn::make('name')
                    ->label('Название')
                    ->searchable() // Включить поиск по этому полю
                    ->sortable(),  // Включить сортировку

                TextColumn::make('category.name') // Отображение имени категории
                ->label('Категория')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB', true) // Форматирование как деньги (RUB - код валюты)
                    ->sortable(),

                TextColumn::make('stock_quantity')
                    ->label('Остаток')
                    ->sortable(),

                BooleanColumn::make('is_active') // Отображение true/false как иконки
                ->label('Активен')
                    ->sortable(),

                BooleanColumn::make('is_featured')
                    ->label('Рекомендуемый')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')->label('Название')->searchable()->sortable(),
                TextColumn::make('reviews_count') // Отображаем количество отзывов
                ->counts('reviews') // Если у вас есть отношение reviews()
                ->label('Отзывы')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id') // Фильтр по категории
                ->label('Категория')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active') // Фильтр Да/Нет/Все
                ->label('Активен'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Рекомендуемый'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Действие "Просмотр"
                Tables\Actions\EditAction::make(), // Действие "Редактировать"
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('manage_reviews')
                    ->label('Отзывы')
                    ->icon('heroicon-s-chat-bubble-left-right') // Иконка
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.product-reviews', $record))
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(), // Массовое удаление
            ]);

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            // 'view' => Pages\ViewProduct::route('/{record}'), // Если нужна отдельная страница просмотра
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'product-reviews' => Pages\ManageProductReviews::route('/{record}/reviews'), // <--- Наша новая страница
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'sku'];
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['category', 'primaryImage']);
    }

    public static function getTranslatableLocales(): array
    {
        return ['ru', 'uz'];
    }

}
