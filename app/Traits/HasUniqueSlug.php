<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUniqueSlug
{
    /**
     * Генерация уникального slug.
     *
     * @param  string  $name  // Строка, из которой генерируется slug (обычно имя или заголовок)
     * @param  int|null  $excludeId  ID текущей модели, который нужно исключить из проверки (при обновлении)
     * @param  string  $slugColumn  // Имя колонки slug в таблице модели (по умолчанию 'slug')
     */
    protected function generateUniqueSlug(string $name, ?int $excludeId = null, string $slugColumn = 'slug'): string
    {
        // Определяем, какой класс модели использует этот трейт
        // Это необходимо, чтобы делать запросы к правильной таблице
        /** @var Model $modelInstance */
        $modelInstance = new static; // Создаем экземпляр текущего класса, использующего трейт

        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while ($this->slugExistsInModel($modelInstance, $slug, $slugColumn, $excludeId)) {
            $slug = $originalSlug.'-'.$count++;
        }

        return $slug;
    }

    /**
     * Проверка существования slug в указанной модели.
     *
     * @param  Model  $modelInstance  Экземпляр модели для запроса
     */
    protected function slugExistsInModel(Model $modelInstance, string $slug, string $slugColumn, ?int $excludeId = null): bool
    {
        $query = $modelInstance->newQuery()->where($slugColumn, $slug);

        if ($excludeId !== null) {
            $query->where($modelInstance->getKeyName(), '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Статический метод для использования извне, если модель еще не инстанциирована
     * или для удобства, если не хотим создавать экземпляр трейта в сервисе.
     * Однако, лучше использовать нестатический метод в сервисах,
     * передавая модель или имя класса модели, если нужно.
     *
     * Этот метод более сложен в использовании из сервисов, так как требует передачи имени класса.
     * Оставим его закомментированным как альтернативу, но вариант с `new static` в `generateUniqueSlug`
     * и использованием трейта в моделях (если там нужна эта логика) или передачей модели в сервис - предпочтительнее.
     */
    /*
    public static function generateUniqueSlugForModel(string $modelClass, string $name, ?int $excludeId = null, string $slugColumn = 'slug'): string
    {
        if (!class_exists($modelClass) || !is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("Class {$modelClass} must be a valid Eloquent Model.");
        }

        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        $instance = new $modelClass; // Для получения имени ключа

        $queryBuilder = function ($currentSlug) use ($modelClass, $slugColumn, $excludeId, $instance) {
            $q = $modelClass::where($slugColumn, $currentSlug);
            if ($excludeId !== null) {
                $q->where($instance->getKeyName(), '!=', $excludeId);
            }
            return $q->exists();
        };

        while ($queryBuilder($slug)) {
            $slug = $originalSlug . '-' . $count++;
        }
        return $slug;
    }
    */

    /**
     * Рекомендуемый способ использования из сервисов:
     * Сервис должен знать, для какой модели он генерирует slug.
     *
     * @param  string  $modelClass  Имя класса модели, например, Product::class
     */
    protected function generateModelUniqueSlug(string $modelClass, string $name, ?int $excludeId = null, string $slugColumn = 'slug'): string
    {
        if (! class_exists($modelClass) || ! is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("Class {$modelClass} must be a valid Eloquent Model.");
        }

        /** @var Model $modelInstance */
        $modelInstance = new $modelClass;

        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while ($this->slugExistsInSpecificModel($modelInstance, $slug, $slugColumn, $excludeId)) {
            $slug = $originalSlug.'-'.$count++;
        }

        return $slug;
    }

    protected function slugExistsInSpecificModel(Model $modelInstance, string $slug, string $slugColumn, ?int $excludeId = null): bool
    {
        $query = $modelInstance->newQuery()->where($slugColumn, $slug);
        if ($excludeId !== null) {
            $query->where($modelInstance->getKeyName(), '!=', $excludeId);
        }

        return $query->exists();
    }
}
