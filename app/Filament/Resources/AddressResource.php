<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AddressResource\Pages;
use App\Models\Address;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AddressResource extends Resource
{
    protected static ?string $model = Address::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Менеджмент';
    protected static ?string $modelLabel = 'Адрес';
    protected static ?string $pluralModelLabel = 'Адреса';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('type')
                    ->label('Тип адреса')
                    ->options([
                        'shipping' => 'Адрес доставки',
                        'billing' => 'Адрес для счета',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('full_name')
                    ->label('ФИО')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone_number')
                    ->label('Телефон')
                    ->tel()
                    ->required(),

                Forms\Components\TextInput::make('address_line_1')
                    ->label('Адрес (строка 1)')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('address_line_2')
                    ->label('Адрес (строка 2)')
                    ->maxLength(255),

                Forms\Components\TextInput::make('city')
                    ->label('Город')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('state_province')
                    ->label('Область/Регион')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('postal_code')
                    ->label('Почтовый индекс')
                    ->required()
                    ->maxLength(20),

                Forms\Components\TextInput::make('country_code')
                    ->label('Код страны')
                    ->required()
                    ->maxLength(2),

                Forms\Components\Toggle::make('is_default')
                    ->label('Адрес по умолчанию')
                    ->default(false),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'shipping' => 'Адрес доставки',
                        'billing' => 'Адрес для счета',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('Город')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('state_province')
                    ->label('Область/Регион')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BooleanColumn::make('is_default')
                    ->label('По умолчанию')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип адреса')
                    ->options([
                        'shipping' => 'Адрес доставки',
                        'billing' => 'Адрес для счета',
                    ]),

                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('По умолчанию'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAddresses::route('/'),
            'create' => Pages\CreateAddress::route('/create'),
            'edit' => Pages\EditAddress::route('/{record}/edit'),
        ];
    }
}
