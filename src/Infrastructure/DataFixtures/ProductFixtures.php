<?php

declare(strict_types=1);

namespace App\Infrastructure\DataFixtures;

use App\Application\Product\Service\ProductFactoryService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Fixture class for loading product sample data into the database.
 */
class ProductFixtures extends Fixture
{
    /**
     * Constructor.
     *
     * @param ProductFactoryService $productFactoryService Service for creating product entities
     */
    public function __construct(private ProductFactoryService $productFactoryService)   {}

    /**
     * Load sample product data into the database.
     *
     * This method creates three products with different names, prices, stocks, and minimum stocks,
     * then persists them to the database.
     *
     * @param ObjectManager $manager The entity manager to use for persisting the products
     * @return void
     */
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
