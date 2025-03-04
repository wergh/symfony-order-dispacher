<?php

namespace App\Tests\Application\Order\UseCase;

use App\Application\Order\UseCase\UpdateStocksUseCase;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\ValueObject\OrderConcept;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Tests\Infrastructure\Factory\MockRepositoryFactory;
use PHPUnit\Framework\TestCase;

class UpdateStocksUseCaseTest extends TestCase
{
    private MockRepositoryFactory $mockRepositoryFactory;
    private ProductRepositoryInterface $productRepository;
    private UpdateStocksUseCase $useCase;

    protected function setUp(): void
    {
        $this->mockRepositoryFactory = new MockRepositoryFactory();
        $this->productRepository = $this->mockRepositoryFactory->createProductRepository();
        $this->useCase = new UpdateStocksUseCase($this->mockRepositoryFactory);
    }

    public function testProductNotFound(): void
    {
        $order = new Order();
        $orderConcept = new OrderConcept($order, 1,"Product A", 12.23, 10, 1);
        $order->addConcept($orderConcept);
        $this->mockRepositoryFactory->expectProductRepositoryFindById(1, null);

        $this->expectException(EntityNotFoundException::class);

        $this->useCase->execute($order);
    }

    public function testDecreaseStock(): void
    {
        $order = new Order();
        $orderConcept = new OrderConcept($order, 1,"Product A", 12.23, 10, 1);
        $order->addConcept($orderConcept);
        $mockProduct = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['hasEnoughStock', 'decreaseStock'])
            ->getMock();

        $initialStock = 50;

        $mockProduct->method('hasEnoughStock')
            ->willReturnCallback(fn($quantity) => $initialStock >= $quantity);

        $mockProduct->method('decreaseStock')
            ->willReturnCallback(function ($quantity) use (&$initialStock) {
                $initialStock -= $quantity;
            });

        $this->mockRepositoryFactory->expectProductRepositoryFindById(1, $mockProduct);

        $this->useCase->execute($order);

        $this->assertSame(49, $initialStock);

    }
}
