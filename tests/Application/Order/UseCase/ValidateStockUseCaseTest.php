<?php

namespace App\Tests\Application\Order\UseCase;

use App\Application\Order\UseCase\ValidateStockUseCase;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\ValueObject\OrderConcept;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Tests\Infrastructure\Factory\MockRepositoryFactory;
use PHPUnit\Framework\TestCase;

class ValidateStockUseCaseTest extends TestCase
{
    private MockRepositoryFactory $mockRepositoryFactory;
    private ProductRepositoryInterface $productRepository;

    private ValidateStockUseCase $useCase;

    protected function setUp(): void
    {
        $this->mockRepositoryFactory = new MockRepositoryFactory();
        $this->productRepository = $this->mockRepositoryFactory->createProductRepository();
        $this->useCase = new ValidateStockUseCase($this->mockRepositoryFactory);
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

    public function testHasEnoughStock(): void
    {
        $order = new Order();
        $orderConcept = new OrderConcept($order, 1,"Product A", 12.23, 10, 1);
        $order->addConcept($orderConcept);

        $mockProduct = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['hasEnoughStock'])
            ->getMock();

        $initialStock = 50;
        $calledWith = null;

        $mockProduct->method('hasEnoughStock')
            ->willReturnCallback(function ($quantity) use ($initialStock, &$calledWith) {
                $calledWith = $quantity;
                return $initialStock >= $quantity;
            });

        $this->mockRepositoryFactory->expectProductRepositoryFindById(1, $mockProduct);

        $this->useCase->execute($order);

        $this->assertSame(1, $calledWith, );
        $this->assertTrue($initialStock >= $calledWith);
    }
}
