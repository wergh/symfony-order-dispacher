<?php

declare(strict_types=1);

namespace App\Application\Product\Service;

use App\Domain\Product\Entity\Product;
use App\Domain\Product\Factory\ProductFactory;

/**
 * Service for creating products using the ProductFactory.
 */
class ProductFactoryService
{
    /**
     * ProductFactoryService constructor.
     *
     * @param ProductFactory $productFactory The factory responsible for creating products.
     */
    public function __construct(private ProductFactory $productFactory)
    {
    }

    /**
     * Creates a new product using the provided details.
     *
     * @param string $name  The name of the product.
     * @param float  $price The price of the product.
     * @param int    $tax   The tax for the product.
     * @param int    $stock The stock quantity of the product.
     *
     * @return Product The newly created product.
     */
    public function create(string $name, float $price, int $tax, int $stock): Product
    {
        return $this->productFactory->create($name, $price, $tax, $stock);
    }
}
