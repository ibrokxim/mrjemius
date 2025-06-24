<?php

namespace App\Repositories;

use App\Models\Category as Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository extends CoreRepository implements CategoryRepositoryInterface
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function findBySlug(string $slug): Model
    {
        return $this->startConditions()
            ->with('products')
            ->where('slug', $slug)
            ->first();
    }

    public function getAll(): Collection
    {
        return $this->startConditions()->all()->where('is_active', 1);
    }


    public function getAllWithChildren(array $filters = []): Collection
    {
        $query = $this->startConditions()
            ->whereNull('parent_id')
            ->with(['children' => function ($query) use ($filters) {
                if (isset($filters['is_active'])) {
                    $query->where('is_active', (bool)$filters['is_active']);
                }
                $query->orderBy('sort_order', 'asc');
            }])
            ->orderBy('sort_order', 'asc');

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool)$filters['is_active']);
        }

        return $query->get();
    }
}
