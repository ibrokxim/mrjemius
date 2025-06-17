<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProductResource;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

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
}
