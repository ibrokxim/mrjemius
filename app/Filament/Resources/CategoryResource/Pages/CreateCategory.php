<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Models\Category;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CategoryResource;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
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