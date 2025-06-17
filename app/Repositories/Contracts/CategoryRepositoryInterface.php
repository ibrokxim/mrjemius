<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function findBySlug(string $slug): ?Category;

    public function getAll(): Collection;
}
