<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProductResource;

class CreateProduct extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
//    protected function getRedirectUrl(): string
//    {
//        return $this->getResource()::getUrl('index');
//    }

    protected function afterCreate(): void
    {
        /** @var Product $record */
        $record = $this->record;
        $seoData = $this->data['seo_data'] ?? null; // Получаем данные из временного statePath

        if ($seoData && method_exists($record, 'saveSeo')) {
            $locale = $seoData['locale'] ?? null;
            unset($seoData['locale']);
            $record->saveSeo($seoData, $locale);
        }
    }

    public function getHeading(): string
    {
        return 'Создание продукта';
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
}
