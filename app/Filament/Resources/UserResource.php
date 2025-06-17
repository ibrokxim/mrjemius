<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = 'Пользователь';
    protected static ?string $pluralModelLabel = 'Пользователи';

    protected static ?string $navigationGroup = 'Менеджмент';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone_number')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('telegram_username')
                    ->maxLength(255),
                TextInput::make('telegram_first_name')
                    ->maxLength(255),
                TextInput::make('telegram_last_name')
                    ->maxLength(255),
                TextInput::make('telegram_photo_url')
                    ->url()
                    ->maxLength(255),
                TextInput::make('loyalty_points')
                    ->numeric()
                    ->default(0),
                TextInput::make('loyalty_card_number')
                    ->maxLength(255),
                Toggle::make('is_admin')
                    ->default(false),
                DateTimePicker::make('email_verified_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone_number')
                    ->label('Номер')
                    ->searchable(),
                TextColumn::make('telegram_username')
                    ->label('Telegram user')
                    ->searchable(),
                TextColumn::make('loyalty_points')
                    ->label('Кол-во баллов')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_admin')
                    ->label('Админ?')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
