<?php

declare(strict_types=1);

namespace App\Domain\Order\Repository;

use App\Domain\Order\Entity\Order;

/**
 * Interface for interacting with the Order entity in the persistence layer.
 *
 * This interface defines the methods for retrieving and saving orders from the database.
 */
interface OrderRepositoryInterface
{
    /**
     * Find an order by its ID.
     *
     * @param int $id The ID of the order to retrieve.
     *
     * @return Order|null The order with the specified ID, or null if not found.
     */
    public function findById(int $id): ?Order;

    /**
     * Save an order to the database.
     *
     * This method persists the given order entity.
     *
     * @param Order $order The order entity to be saved.
     */
    public function save(Order $order): void;
}
