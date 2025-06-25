<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Загружаем SEO данные в форму
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Product $record */
        $record = $this->record;
        if ($record->seo) { // Если связь seo существует
            $data['seo_data'] = $record->seo->toArray();
            // Если у вас есть поле locale в seo_data, которое не является частью модели SeoMeta, но используется для фильтрации:
            // $data['seo_data']['locale'] = $record->seo->locale; // Или текущая локаль
        }
        return $data;
    }


    protected function afterSave(): void // afterSave вызывается и при обновлении
    {
        /** @var Product $record */
        $record = $this->record;
        $seoData = $this->data['seo_data'] ?? null;

        if ($seoData && method_exists($record, 'saveSeo')) {
            $locale = $seoData['locale'] ?? null;
            unset($seoData['locale']);
            $record->saveSeo($seoData, $locale);
        }
    }
}
