<?php

declare(strict_types=1);

namespace App\Application\Product\Service;


use App\Domain\Product\Entity\Product;
use App\Domain\Product\Factory\ProductFactory;

class ProductFactoryService
{

    public function __construct(private ProductFactory $productFactory)
    {
    }

    public function create(string $name, float $price, int $tax, int $stock): Product
    {
        return $this->productFactory->create($name, $price, $tax, $stock);
    }
}
