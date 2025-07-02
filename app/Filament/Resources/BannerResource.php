<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class BannerResource extends Resource
{
    use Translatable;

    protected static ?string $model = Banner::class;
    protected static ?string $pluralModelLabel = 'Акции и баннеры';
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основная информация')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Заголовок баннера')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('link_url')
                            ->label('URL-ссылка (куда ведет баннер)')
                            ->url()
                            ->placeholder('https://example.com/product/123')
                            ->helperText('Если оставить пустым, баннер не будет кликабельным.')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Описание (для себя)')
                            ->columnSpanFull(),
                    ]),

                Section::make('Настройка отображения')
                    ->description('Здесь вы можете управлять тем, как баннер будет выглядеть на разных устройствах.')
                    ->schema([
                        // --- НАША ШПАРГАЛКА ---
                        Placeholder::make('info_placeholder')
                            ->label('Краткая инструкция')
                            ->content(new HtmlString(
                                '<ul>' .
                                '<li><strong>Точка фокуса:</strong> Укажите, какая часть изображения самая важная. Она всегда будет оставаться в центре.</li>' .
                                '<li><strong>Высота блока:</strong> Увеличьте это значение, чтобы в баннер "поместилось" больше изображения по вертикали (эффект "отдаления").</li>' .
                                '</ul>'
                            )),
                    ]),

                Section::make('Изображение для десктопа (широкое)')
                    ->description('Рекомендуемый размер: 1920x500 пикселей.')
                    ->collapsible() // Секцию можно будет сворачивать
                    ->schema([
                        FileUpload::make('desktop_image_url')
                            ->label('Загрузить изображение')
                            ->image()
                            ->directory('banners/desktop') // Изображения будут сохраняться в storage/app/public/banners/desktop
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('desktop_height')
                            ->label('Высота блока (в пикселях, для десктопа)')
                            ->numeric()
                            ->default(400),

                        TextInput::make('desktop_bg_position')
                            ->label('Точка фокуса (для десктопа)')
                            ->placeholder('center center')
                            ->default('center center')
                            ->helperText(new HtmlString('<b>Популярные значения:</b><br>' .
                                '<code>center center</code> - центр картинки<br>' .
                                '<code>center top</code> - центр по ширине, верх по высоте<br>' .
                                '<code>center bottom</code> - центр по ширине, низ по высоте<br>' .
                                '<code>50% 25%</code> - тонкая настройка (X и Y)'))
                    ])->columns(2),


                Section::make('Изображение для мобильных (квадратное или вертикальное)')
                    ->description('Рекомендуемый размер: 800x800 или 800x1000 пикселей.')
                    ->collapsible()
                    ->schema([
                        FileUpload::make('mobile_image_url')
                            ->label('Загрузить изображение')
                            ->image()
                            ->directory('banners/mobile')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('mobile_height')
                            ->label('Высота блока (в пикселях, для мобильных)')
                            ->numeric()
                            ->default(350)
                            ->required(),


                        TextInput::make('mobile_bg_position')
                            ->label('Точка фокуса (для мобильных)')
                            ->placeholder('center center')
                            ->default('center center')
                            ->required()
                            ->helperText(new HtmlString('<b>Популярные значения:</b><br>' .
                                '<code>center center</code> - центр картинки<br>' .
                                '<code>center top</code> - центр по ширине, верх по высоте<br>' .
                                '<code>center bottom</code> - центр по ширине, низ по высоте<br>' .
                                '<code>50% 25%</code> - тонкая настройка (X и Y)'))
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('desktop_image_url')
                    ->label('Изображение (десктоп)')
                    ->width(150)
                    ->height('auto'),

                TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('link_url')
                    ->label('Ссылка')
                    ->toggleable(isToggledHiddenByDefault: true) // Скрываем по умолчанию для чистоты
                    ->url(fn(?string $state): ?string => $state, true), // Делаем ссылку кликабельной

                ToggleColumn::make('is_active') // Позволяет менять статус прямо из таблицы
                ->label('Активен'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }

    public static function getTranslatableLocales(): array
    {
        return ['ru', 'uz'];
    }
}
