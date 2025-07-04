<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Actions;
use App\Models\Category;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CategoryResource;

class CreateCategory extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
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

    protected function afterCreate(): void
    {
        /** @var Category $record */
        $record = $this->record;
        $seoData = $this->data['seo_data'] ?? null; // Получаем данные из временного statePath

        if ($seoData && method_exists($record, 'saveSeo')) {
            $locale = $seoData['locale'] ?? null;
            unset($seoData['locale']);
            $record->saveSeo($seoData, $locale);
        }
    }
}
