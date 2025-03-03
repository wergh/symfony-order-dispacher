<?php

declare(strict_types=1);

namespace App\Domain\Product\Factory;

use App\Domain\Product\Entity\Product;

/**
 * Factory class for creating Product instances.
 *
 * This factory is responsible for creating new instances of the Product entity.
 * It ensures the correct creation of products with the required parameters.
 */
class ProductFactory
{
    /**
     * Creates a new Product instance.
     *
     * @param string $name The name of the product.
     * @param float $price The price of the product.
     * @param int $tax The tax percentage applied to the product.
     * @param int $stock The stock quantity available for the product.
     *
     * @return Product The newly created Product instance.
     */
    public function create(string $name, float $price, int $tax, int $stock): Product
    {
        return new Product($name, $price, $tax, $stock);
    }
}
