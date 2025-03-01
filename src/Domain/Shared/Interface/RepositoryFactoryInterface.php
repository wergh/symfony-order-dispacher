<?php

declare(strict_types=1);

namespace App\Domain\Shared\Interface;

use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Domain\Product\Repository\ProductRepositoryInterface;

interface RepositoryFactoryInterface
{

    public function createClientRepository(): ClientRepositoryInterface;
    public function createOrderRepository(): OrderRepositoryInterface;
    public function createProductRepository(): ProductRepositoryInterface;

}
