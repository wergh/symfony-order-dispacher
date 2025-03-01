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

class DoctrineRepositoryFactory implements RepositoryFactoryInterface
{

    public function __construct(private EntityManagerInterface $entityManager) {}

    public function createClientRepository(): ClientRepositoryInterface
    {
        return new DoctrineClientRepository($this->entityManager);
    }

    public function createOrderRepository(): OrderRepositoryInterface
    {
        return new DoctrineOrderRepository($this->entityManager);
    }

    public function createProductRepository(): ProductRepositoryInterface
    {
        return new DoctrineProductRepository($this->entityManager);
    }
}
