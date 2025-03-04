<?php

namespace App\Tests\Application\Product\UseCase;

use App\Application\Product\DTO\UpdateProductStockDTO;
use App\Application\Product\UseCase\UpdateProductStockUseCase;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Tests\Infrastructure\Factory\MockRepositoryFactory;
use PHPUnit\Framework\TestCase;

class UpdateProductoStockUseCaseTest extends TestCase
{

    private MockRepositoryFactory $mockRepositoryFactory;
    private ProductRepositoryInterface $productRepository;

    private UpdateProductStockUseCase $useCase;

    protected function setUp(): void
    {
        $this->mockRepositoryFactory = new MockRepositoryFactory();
        $this->productRepository = $this->mockRepositoryFactory->createProductRepository();
        $this->useCase = new UpdateProductStockUseCase($this->mockRepositoryFactory);
    }


    public function testProductNotFound()
    {
        $this->mockRepositoryFactory->expectProductRepositoryFindById(1, null);

        $dto = new UpdateProductStockDTO(1, 50);

        $this->expectException(EntityNotFoundException::class);

        $this->useCase->execute($dto);

    }

    public function testProductoStockUpdated()
    {
        $mockProduct = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setStock'])
            ->getMock();

        $dto = new UpdateProductStockDTO(1, 10);
        $initialStock = 50;

        $mockProduct->method('setStock')
            ->willReturnCallback(function ($quantity) use ($mockProduct, &$initialStock) {
                $initialStock = $quantity;
                return $mockProduct;
            });

        $this->mockRepositoryFactory->expectProductRepositoryFindById(1, $mockProduct);

        $this->useCase->execute($dto);

        $this->assertSame(10, $initialStock);
    }
}
