<?php

namespace App\Filament\Resources\BannerResource\Pages;

use App\Filament\Resources\BannerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBanner extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
    /**
     * Эта функция позволяет перенаправить пользователя после создания записи
     * на страницу редактирования с активной локалью, которая была выбрана при создании.
     * Это улучшает UX.
     */
    protected function getRedirectUrl(): string
    {
        $record = $this->getRecord();
        $activeLocale = $this->activeLocale;

        if (is_null($activeLocale)) {
            $translatableLocales = static::getResource()::getTranslatableLocales();
            $activeLocale = $translatableLocales[0] ?? app()->getLocale();
        }

        return $this->getResource()::getUrl('edit', [
            'record' => $record,
            'activeLocale'      => $activeLocale,
        ]);
    }
}
