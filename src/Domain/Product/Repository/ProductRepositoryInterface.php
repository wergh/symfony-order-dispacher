<?php

declare(strict_types=1);

namespace App\Domain\Product\Repository;

use App\Domain\Product\Entity\Product;
use Doctrine\Common\Collections\Collection;

/**
 * Interface for the Product repository.
 *
 * This interface defines the contract for a repository responsible for managing
 * the persistence of Product entities, including methods for retrieving, saving,
 * and managing products.
 */
interface ProductRepositoryInterface
{
    /**
     * Retrieves all products.
     *
     * @return Collection The collection of all products.
     */
    public function all(): Collection;

    /**
     * Finds a product by its ID.
     *
     * @param int $id The ID of the product to be retrieved.
     *
     * @return Product|null The product with the given ID, or null if not found.
     */
    public function findById(int $id): ?Product;

    /**
     * Saves a product.
     *
     * @param Product $product The product entity to be saved.
     */
    public function save(Product $product): void;
}
