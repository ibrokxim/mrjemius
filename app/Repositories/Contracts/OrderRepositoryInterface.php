<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;

    public function addItemsToOrder(Order $order, array $itemsData): void ;

    public function findById(int $id): ?Order;

    public function findByOrderNumber(string $orderNumber): ?Order;

    public function getForUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function updateStatus(Order $order, string $status): bool;

}
