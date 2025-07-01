<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\Fieldset;
use Filament\Tables;
use App\Models\Banner;
use Filament\Forms\Form;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\BannerResource\Pages;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Filament\Resources\BannerResource\RelationManagers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Filament\Resources\Concerns\Translatable;
use Intervention\Image\Image;

class BannerResource extends Resource
{
    use Translatable;

    protected static ?string $model = Banner::class;
    protected static ?string $pluralModelLabel = 'Акции и баннеры'; // Название модели во множественном числе

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основная информация')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title') // Это поле теперь будет иметь переключатель языков
                        ->label('Заголовок баннера')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (callable $set, ?string $state) => $set('slug', Str::slug($state))), // Slug будет генерироваться из текущего выбранного языка

                        TextInput::make('slug')
                            ->label('Slug (ЧПУ)')
                            ->maxLength(255)
                            ->unique(Banner::class, 'slug', ignoreRecord: true)
                            ->nullable(),

                        DateTimePicker::make('start_date')
                            ->label('Дата начала показа')
                            ->nullable(),

                        DateTimePicker::make('end_date')
                            ->label('Дата окончания показа')
                            ->nullable(),

                        FileUpload::make('banner_image_url')
                            ->label('Изображение баннера')
                            ->image()
                            ->directory('banner-images')
                            ->visibility('public')
                            ->nullable()
                            ->afterStateUpdated(fn ($state) => \Log::info('Загружен файл:', ['state' => $state])),
                        Fieldset::make('Размеры изображения (необязательно)')
                            ->schema([
                                TextInput::make('width')
                                    ->label('Ширина (в пикселях)')
                                    ->numeric()
                                    ->suffix('px') // Добавляет "px" после поля для наглядности
                                    ->nullable(),

                                TextInput::make('height')
                                    ->label('Высота (в пикселях)')
                                    ->numeric()
                                    ->suffix('px')
                                    ->nullable(),
                            ])
                            ->columns(2),
                        Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                    ]),
                Section::make('Описание')
                    ->schema([
                        RichEditor::make('description') // Это поле тоже будет иметь переключатель языков
                        ->label('Описание баннера')
                            ->columnSpanFull()
                            ->nullable(),
                    ])
            ]);
    }

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        return self::processAndResizeImage($data);
    }

    // --- 3. МЕТОД ДЛЯ ОБРАБОТКИ ПЕРЕД ОБНОВЛЕНИЕМ ---
    protected static function mutateFormDataBeforeUpdate(array $data): array
    {
        return self::processAndResizeImage($data);
    }

    /**
     * Вспомогательный метод для обработки, обрезки и сохранения изображения.
     */
    protected static function processAndResizeImage(array $data): array
    {
        // Проверяем, был ли загружен новый файл. В Filament - это строка с временным путем.
        if (isset($data['banner_image_url']) && is_string($data['banner_image_url'])) {

            $originalPath = $data['banner_image_url'];
            $storage = Storage::disk('public');

            // Проверяем, что файл реально существует
            if (!$storage->exists($originalPath)) {
                return $data; // Возвращаем данные без изменений, если файла нет
            }

            // Читаем временный файл
            $image = Image::read($storage->path($originalPath));

            // Определяем целевые размеры из формы
            $targetWidth = $data['width'] ?? 1296;
            $targetHeight = $data['height'] ?? 450;

            // Обрезаем изображение точно до нужных пропорций, центрируя
            $image->cover($targetWidth, $targetHeight, 'center');

            // Создаем новое имя файла и путь для обработанного изображения
            $filename = Str::uuid() . '.' . pathinfo($originalPath, PATHINFO_EXTENSION);
            $processedPath = 'banner-images/processed/' . $filename;

            // Сохраняем обработанное изображение
            $storage->put($processedPath, (string) $image->encode());

            // ПОДМЕНЯЕМ ПУТЬ В ДАННЫХ!
            // Теперь в базу сохранится путь к новому, обрезанному файлу
            $data['banner_image_url'] = $processedPath;
        }

        return $data;
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('banner_image_url')
                    ->label('Изображение')
                    ->defaultImageUrl(url('banner_image_url')),
                TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->sortable(),
                BooleanColumn::make('is_active')
                    ->label('Активна')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
        ;
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
