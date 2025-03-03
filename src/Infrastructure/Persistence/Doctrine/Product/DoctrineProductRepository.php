<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Product;

use App\Domain\Product\Entity\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Doctrine implementation of the ProductRepositoryInterface.
 *
 * This class provides data access to Product entities using Doctrine ORM.
 */
class DoctrineProductRepository implements ProductRepositoryInterface
{
    /**
     * The Doctrine entity manager.
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager The Doctrine entity manager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retrieves all products from the database.
     *
     * @return Collection A collection of Product entities
     */
    public function all(): Collection
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        return new ArrayCollection($products);
    }

    /**
     * Finds a product by its ID.
     *
     * @param int $id The product ID to find
     * @return Product|null The found Product entity or null if not found
     */
    public function findById(int $id): ?Product
    {
        return $this->entityManager->getRepository(Product::class)->find($id);
    }

    /**
     * Saves a product to the database.
     *
     * @param Product $product The product entity to save
     * @return void
     */
    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}
