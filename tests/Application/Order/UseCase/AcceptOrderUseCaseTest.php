<?php

namespace App\Tests\Application\Order\UseCase;


use App\Application\Order\UseCase\AcceptOrderUseCase;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Tests\Infrastructure\Factory\MockRepositoryFactory;
use PHPUnit\Framework\TestCase;

class AcceptOrderUseCaseTest extends TestCase
{

    private MockRepositoryFactory $mockRepositoryFactory;
    private OrderRepositoryInterface $orderRepository;
    private AcceptOrderUseCase $useCase;

    protected function setUp(): void
    {
        $this->mockRepositoryFactory = new MockRepositoryFactory();
        $this->orderRepository = $this->mockRepositoryFactory->createOrderRepository();
        $this->useCase = new AcceptOrderUseCase($this->mockRepositoryFactory);
    }

    public function testExecute(): void
    {
        $mockOrder = $this->createMock(Order::class);
        $mockOrder->expects($this->once())
            ->method('markAsAccepted');
        $mockOrder->expects($this->once())
            ->method('isAccepted')
            ->willReturn(true);
        $this->mockRepositoryFactory->expectOrderRepositorySaveMarkAsAccepted($mockOrder);
        $this->useCase->execute($mockOrder);

    }
}
