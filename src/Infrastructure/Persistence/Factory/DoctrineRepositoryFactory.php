<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Factory;

use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;
use App\Infrastructure\Persistence\Doctrine\Client\DoctrineClientRepository;
use App\Infrastructure\Persistence\Doctrine\Order\DoctrineOrderRepository;
use App\Infrastructure\Persistence\Doctrine\Product\DoctrineProductRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Factory for creating Doctrine repository implementations.
 *
 * This class implements the RepositoryFactoryInterface to provide
 * Doctrine-specific repository implementations for various domain entities.
 */
class DoctrineRepositoryFactory implements RepositoryFactoryInterface
{
    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager The Doctrine entity manager
     */
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * Creates a Doctrine implementation of the ClientRepositoryInterface.
     *
     * @return ClientRepositoryInterface The created client repository
     */
    public function createClientRepository(): ClientRepositoryInterface
    {
        return new DoctrineClientRepository($this->entityManager);
    }

    /**
     * Creates a Doctrine implementation of the OrderRepositoryInterface.
     *
     * @return OrderRepositoryInterface The created order repository
     */
    public function createOrderRepository(): OrderRepositoryInterface
    {
        return new DoctrineOrderRepository($this->entityManager);
    }

    /**
     * Creates a Doctrine implementation of the ProductRepositoryInterface.
     *
     * @return ProductRepositoryInterface The created product repository
     */
    public function createProductRepository(): ProductRepositoryInterface
    {
        return new DoctrineProductRepository($this->entityManager);
    }
}
