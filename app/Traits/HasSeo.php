<?php

namespace App\Traits;

use App\Models\SeoMeta;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeo
{
    public function seo(): MorphOne
    {
        $relation = $this->morphOne(SeoMeta::class, 'model');

        if (array_key_exists('locale', (new SeoMeta())->getFillable())) {
            $relation->where('locale', app()->getLocale());
        }
        return $relation;
    }

    public function getResolvedMetaTitleAttribute(): ?string
    {
        return $this->seo->meta_title ?? $this->getDefaultSeoTitle();
    }

    public function getResolvedMetaDescriptionAttribute(): ?string
    {
        return $this->seo->meta_description ?? $this->getDefaultSeoDescription();
    }

    protected function getDefaultSeoTitle(): ?string
    {
        return $this->name ?? $this->title ?? config('app.name');
    }

    protected function getDefaultSeoDescription(): ?string
    {
        if (isset($this->description)) {
            return Str::limit(strip_tags((string)$this->description), 160);
        }
        return null;
    }

    public function saveSeo(array $seoData, ?string $locale = null): SeoMeta
    {
        $attributes = ['model_id' => $this->getKey(), 'model_type' => $this->getMorphClass()];

        if ($locale === null && array_key_exists('locale', (new SeoMeta)->getFillable())) {
            $locale = App::getLocale();
        }

        if ($locale !== null) {
            $attributes['locale'] = $locale;
        }

        // Удаляем пустые значения из $seoData, чтобы не перезаписывать существующие значения null-ами,
        // если эти поля не были переданы для обновления.
        // Если нужно перезаписывать null-ами, уберите array_filter.
        $filteredSeoData = array_filter($seoData, fn($value) => $value !== null && $value !== '');

        return SeoMeta::updateOrCreate($attributes, $filteredSeoData);

    }

    /**
     * Получить значение конкретного SEO-поля.
     *
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getSeoValue(string $key,  $default = null): mixed
    {
        return $this->seo->{$key} ?? $default;
    }
}
