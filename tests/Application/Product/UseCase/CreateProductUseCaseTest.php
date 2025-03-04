<?php

namespace App\Tests\Application\Product\UseCase;

use App\Application\Product\DTO\CreateProductDto;
use App\Application\Product\UseCase\CreateProductUseCase;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Tests\Infrastructure\Factory\MockRepositoryFactory;
use PHPUnit\Framework\TestCase;

class CreateProductUseCaseTest extends TestCase
{
    private MockRepositoryFactory $mockRepositoryFactory;
    private ProductRepositoryInterface $productRepository;

    private CreateProductUseCase $useCase;

    protected function setUp(): void
    {
        $this->mockRepositoryFactory = new MockRepositoryFactory();
        $this->productRepository = $this->mockRepositoryFactory->createProductRepository();
        $this->useCase = new CreateProductUseCase($this->mockRepositoryFactory);
    }

    public function testExecute()
    {
        $dto = new CreateProductDto('Product', 14.34, 10, 100);
        $this->mockRepositoryFactory->expectProductRepositorySave($this->callback(function($product) {
            $this->assertInstanceOf(Product::class, $product);
            $this->assertEquals('Product', $product->getName());
            $this->assertEquals(14.34, $product->getPrice());
            $this->assertEquals(10, $product->getTax());
            $this->assertEquals(100, $product->getStock());
            return true;
        }));
        $this->useCase->execute($dto);

    }
}
