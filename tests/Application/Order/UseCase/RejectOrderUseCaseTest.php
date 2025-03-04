<?php

namespace App\Tests\Application\Order\UseCase;

use App\Application\Order\UseCase\RejectOrderUseCase;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Tests\Infrastructure\Factory\MockRepositoryFactory;
use PHPUnit\Framework\TestCase;

class RejectOrderUseCaseTest extends TestCase
{
    private MockRepositoryFactory $mockRepositoryFactory;
    private OrderRepositoryInterface $orderRepository;
    private RejectOrderUseCase $useCase;

    protected function setUp(): void
    {
        $this->mockRepositoryFactory = new MockRepositoryFactory();
        $this->orderRepository = $this->mockRepositoryFactory->createOrderRepository();
        $this->useCase = new RejectOrderUseCase($this->mockRepositoryFactory);
    }

    public function testExecute(): void
    {
        $mockOrder = $this->createMock(Order::class);
        $mockOrder->expects($this->once())
            ->method('markAsRejected');
        $mockOrder->expects($this->once())
            ->method('isAccepted')
            ->willReturn(false);
        $this->mockRepositoryFactory->expectOrderRepositorySaveMarkAsRejected($mockOrder);
        $this->useCase->execute($mockOrder);

    }
}
