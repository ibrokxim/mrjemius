<?php

namespace App\Repositories;

use App\Models\Product as Model;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository extends CoreRepository implements ProductRepositoryInterface
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function getProductBySlug($slug): ?Model
    {
        return $this->startConditions()
            ->with(['category', 'images', 'tags', 'reviews'])
            ->where('slug', $slug)
            ->first();
    }

    public function getBestSellerProducts($limit = 10)
    {
        return $this->startConditions()::with(['category', 'primaryImage'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getAllProducts()
    {
        return $this->startConditions()::with(['category', 'primaryImage'])->paginate(20);
    }


    public function getForCategory(int $categoryId, int $perPage = 12, array $filters = [], array $sortBy = []): LengthAwarePaginator
    {
        $query = $this->startConditions()
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->with('primaryImage', 'category');

        // --- ЛОГИКА ФИЛЬТРАЦИИ ---
        if (!empty($filters['price_from'])) {
            $query->where('price', '>=', $filters['price_from']);
        }
        if (!empty($filters['price_to'])) {
            $query->where('price', '=<', $filters['price_to']);
        }

        // Фильтр по рейтингу (предполагаем, что у вас есть поле `rating` в таблице `products`)
        if (!empty($filters['rating'])) {
            $query->having('reviews_avg_rating', '>=', (int)$filters['rating']);
        }

        // --- ЛОГИКА СОРТИРОВКИ ---
        if (!empty($sortBy)) {
            foreach ($sortBy as $column => $direction) {
                // Если сортировка по рейтингу, используем вычисленное поле
                if ($column === 'rating') {
                    $column = 'reviews_avg_rating';
                }

                if (in_array($column, ['price', 'reviews_avg_rating', 'created_at', 'name'])) {
                    $query->orderBy($column, $direction);
                }
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage)->withQueryString(); // withQueryString() сохранит параметры фильтра в ссылках пагинации
    }

    public function search(array $searchData, int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->startConditions()
            ->where('is_active', true)
            ->with('primaryImage', 'category');

        if (!empty($searchData['search_term'])) {
            $searchTerm = $searchData['search_term'];

            // Ищем по нескольким полям: названию, описанию, артикулу (sku)
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('sku', 'LIKE', "%{$searchTerm}%");
                // Можно добавить поиск по атрибутам или тегам, если нужно
            });
        }

        return $query->paginate($perPage)->withQueryString(); // withQueryString очень важен для пагинации на странице результатов
}
}
