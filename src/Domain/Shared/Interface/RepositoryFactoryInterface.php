<?php

declare(strict_types=1);

namespace App\Domain\Shared\Interface;

use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Domain\Product\Repository\ProductRepositoryInterface;

/**
 * Interface for a repository factory.
 *
 * This interface defines methods for creating repositories for different domain entities.
 * It provides a consistent way to obtain repositories for the Client, Order, and Product entities.
 *
 * @package App\Domain\Shared\Interface
 */
interface RepositoryFactoryInterface
{
    /**
     * Creates a repository for the Client entity.
     *
     * @return ClientRepositoryInterface The repository for the Client entity.
     */
    public function createClientRepository(): ClientRepositoryInterface;

    /**
     * Creates a repository for the Order entity.
     *
     * @return OrderRepositoryInterface The repository for the Order entity.
     */
    public function createOrderRepository(): OrderRepositoryInterface;

    /**
     * Creates a repository for the Product entity.
     *
     * @return ProductRepositoryInterface The repository for the Product entity.
     */
    public function createProductRepository(): ProductRepositoryInterface;
}
