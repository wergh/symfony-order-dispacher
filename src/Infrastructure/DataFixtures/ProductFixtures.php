<?php

declare(strict_types=1);

namespace App\Infrastructure\DataFixtures;

use App\Application\Product\Service\ProductFactoryService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function __construct(private ProductFactoryService $productFactoryService)   {}

    public function load(ObjectManager $manager): void
    {
        $product =$this->productFactoryService->create("Producto A", 10.99, 10, 5);
        $manager->persist($product);
        $manager->flush();
        $product =$this->productFactoryService->create("Producto B", 19.99, 15, 10);
        $manager->persist($product);
        $manager->flush();
        $product =$this->productFactoryService->create("Producto C", 5.99, 21, 20);
        $manager->persist($product);
        $manager->flush();
    }

}
