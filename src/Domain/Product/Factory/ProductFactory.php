<?php

declare(strict_types=1);

namespace App\Domain\Product\Factory;

use App\Domain\Product\Entity\Product;

class ProductFactory
{

    public function create(string $name, float $price, int $tax, int $stock): Product
    {
        return new Product($name, $price, $tax, $stock);
    }
}
