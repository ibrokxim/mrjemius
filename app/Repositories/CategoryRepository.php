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
        return $this->startConditions()->all();
    }
}
