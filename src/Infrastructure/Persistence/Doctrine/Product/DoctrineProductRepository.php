<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Product;

use App\Domain\Product\Entity\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineProductRepository implements ProductRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function all(): Collection
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        return new ArrayCollection($products);
    }

    public function findById(int $id): ?Product
    {
        return $this->entityManager->getRepository(Product::class)->find($id);
    }

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}
