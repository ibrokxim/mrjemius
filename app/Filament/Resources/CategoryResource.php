<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Каталог';
    protected static ?string $modelLabel = 'Категория';
    protected static ?string $pluralModelLabel = 'Категории';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Основная информация и SEO')
                    ->tabs([
                        Tabs\Tab::make('Основное')
                            ->schema([
                                Section::make('Основная информация')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Название')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (callable $set, ?string $state) => $set('slug', Str::slug($state))),

                                        TextInput::make('slug')
                                            ->label('Slug (ЧПУ)')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(Category::class, 'slug', ignoreRecord: true),

                                        Select::make('parent_id')
                                            ->label('Родительская категория')
                                            ->relationship('parent', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->nullable(),

                                        TextInput::make('sort_order')
                                            ->label('Порядок сортировки')
                                            ->integer()
                                            ->default(0),
                                    ]),

                                Section::make('Описание и статус')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\RichEditor::make('description')
                                            ->label('Описание')
                                            ->columnSpanFull()
                                            ->nullable(),

                                        Toggle::make('is_active')
                                            ->label('Активна')
                                            ->default(true),

                                        FileUpload::make('image_url')
                                            ->label('Изображение категории')
                                            ->image()
                                            ->directory('category-images')
                                            ->visibility('public')
                                            ->nullable(),
                                    ]),
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
                                        FileUpload::make('og_image_url')
                                            ->label('OpenGraph Image')
                                            ->image()
                                            ->directory('seo-og-images')
                                            ->visibility('public')
                                            ->nullable(),
                                        TextInput::make('canonical_url')->label('Канонический URL')->url()->nullable(),
                                        TextInput::make('robots_tags')->label('Robots Tag'),
                                    ])->columns(1)
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Изображение')
                    ->defaultImageUrl(url('/placeholder.jpg')),

                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->label('Родительская категория')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('products_count')
                    ->label('Кол-во товаров')
                    ->counts('products')
                    ->sortable(),

                BooleanColumn::make('is_active')
                    ->label('Активна')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('parent_id')
                    ->label('Родительская категория')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_active')
                    ->label('Активна'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Здесь можно определить Relation Managers, если нужно
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}