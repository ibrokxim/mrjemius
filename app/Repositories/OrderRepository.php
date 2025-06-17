<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository extends CoreRepository implements OrderRepositoryInterface
{

    protected function getModelClass()
    {
        // TODO: Implement getModelClass() method.
    }

    public function create(array $data): Order
    {
        // TODO: Implement create() method.
    }

    public function addItemsToOrder(Order $order, array $itemsData): void
    {
        // TODO: Implement addItemsToOrder() method.
    }

    public function findById(int $id): ?Order
    {
        // TODO: Implement findById() method.
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        // TODO: Implement findByOrderNumber() method.
    }

    public function getForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        // TODO: Implement getForUser() method.
    }

    public function updateStatus(Order $order, string $status): bool
    {
        // TODO: Implement updateStatus() method.
    }
}
