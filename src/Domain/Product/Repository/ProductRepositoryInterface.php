<?php

declare(strict_types=1);

namespace App\Domain\Product\Repository;

use App\Domain\Product\Entity\Product;
use Doctrine\Common\Collections\Collection;

interface ProductRepositoryInterface
{

    public function all(): Collection;
    public function findById(int $id): ?Product;
    public function save(Product $product): void;
}
