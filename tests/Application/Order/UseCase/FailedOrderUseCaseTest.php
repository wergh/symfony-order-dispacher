<?php

namespace App\Tests\Application\Order\UseCase;

use App\Application\Order\UseCase\FailedOrderUseCase;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Tests\Infrastructure\Factory\MockRepositoryFactory;
use PHPUnit\Framework\TestCase;

class FailedOrderUseCaseTest extends TestCase
{
    private MockRepositoryFactory $mockRepositoryFactory;
    private OrderRepositoryInterface $orderRepository;
    private FailedOrderUseCase $useCase;

    protected function setUp(): void
    {
        $this->mockRepositoryFactory = new MockRepositoryFactory();
        $this->orderRepository = $this->mockRepositoryFactory->createOrderRepository();
        $this->useCase = new FailedOrderUseCase($this->mockRepositoryFactory);
    }

    public function testExecute(): void
    {
        $mockOrder = $this->createMock(Order::class);
        $mockOrder->expects($this->once())
            ->method('markAsFailed');
        $mockOrder->expects($this->once())
            ->method('isAccepted')
            ->willReturn(false);
        $this->mockRepositoryFactory->expectOrderRepositorySaveMarkAsFailed($mockOrder);
        $this->useCase->execute($mockOrder);

    }
}
