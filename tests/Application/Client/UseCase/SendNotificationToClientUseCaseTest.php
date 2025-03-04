<?php

namespace App\Tests\Application\Client\UseCase;

use App\Application\Client\UseCase\SendNotificationToClientUseCase;
use App\Application\Order\DTO\OrderProcessedDto;
use App\Domain\Client\Entity\Client;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Tests\Infrastructure\Factory\MockRepositoryFactory;
use PHPUnit\Framework\TestCase;

class SendNotificationToClientUseCaseTest extends TestCase
{

    private MockRepositoryFactory $mockRepositoryFactory;
    private OrderRepositoryInterface $orderRepository;
    private LoggerInterface $mockLogger;
    private SendNotificationToClientUseCase $useCase;


    protected function setUp(): void
    {
        $this->mockRepositoryFactory = new MockRepositoryFactory();
        $this->orderRepository = $this->mockRepositoryFactory->createOrderRepository();
        $this->mockLogger = $this->createMock(LoggerInterface::class);
    }

    public function testOrderNotFound(): void
    {
        $this->mockRepositoryFactory->expectOrderRepositoryFindById(1, null);

        $useCase = new SendNotificationToClientUseCase($this->mockLogger, $this->mockRepositoryFactory);

        $this->expectException(EntityNotFoundException::class);

        $orderProcessedDto = new OrderProcessedDto(1, 'Order not found');
        $useCase->execute($orderProcessedDto);
    }

    public function testExecuteWithAcceptedOrder(): void
    {
        $orderProcessedDto = new OrderProcessedDto(1, 'Order accepted');

        $mockClient = $this->createMock(Client::class);
        $mockClient->method('getName')->willReturn('John Doe');
        $mockOrder = $this->createMock(Order::class);
        $mockOrder->method('isAccepted')->willReturn(true);
        $mockOrder->method('getClient')->willReturn($mockClient);

        $this->mockRepositoryFactory->expectOrderRepositoryFindById(1, $mockOrder);

        $this->mockLogger->expects($this->once())
            ->method('info')
            ->with($this->callback(function ($message){
                return str_contains($message, "The order has been successfully completed for client");
            }));

        $this->useCase = new SendNotificationToClientUseCase($this->mockLogger, $this->mockRepositoryFactory);
        $this->useCase->execute($orderProcessedDto);

        $this->assertTrue(true);
    }

    public function testExecuteWithNoAcceptedOrder(): void
    {
        $orderProcessedDto = new OrderProcessedDto(1, 'Order accepted');

        $mockClient = $this->createMock(Client::class);
        $mockClient->method('getName')->willReturn('John Doe');
        $mockOrder = $this->createMock(Order::class);
        $mockOrder->method('isAccepted')->willReturn(false);
        $mockOrder->method('getClient')->willReturn($mockClient);

        $this->mockRepositoryFactory->expectOrderRepositoryFindById(1, $mockOrder);

        $this->mockLogger->expects($this->once())
            ->method('error')
            ->with($this->callback(function ($message){
                return str_contains($message, "The order has been denied due to");
            }));

        $this->useCase = new SendNotificationToClientUseCase($this->mockLogger, $this->mockRepositoryFactory);
        $this->useCase->execute($orderProcessedDto);

        $this->assertTrue(true);
    }
}
