<?php

namespace App\Tests\Application\Order\UseCase;

use App\Application\Order\DTO\OrderProcessorDto;
use App\Application\Order\UseCase\GetOrderToProcessUseCase;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Tests\Infrastructure\Factory\MockRepositoryFactory;
use PHPUnit\Framework\TestCase;

class GetOrderToProcessUseCaseTest extends TestCase
{

    private MockRepositoryFactory $mockRepositoryFactory;
    private OrderRepositoryInterface $orderRepository;
    private GetOrderToProcessUseCase $useCase;

    protected function setUp(): void
    {
        $this->mockRepositoryFactory = new MockRepositoryFactory();
        $this->orderRepository = $this->mockRepositoryFactory->createOrderRepository();
        $this->useCase = new GetOrderToProcessUseCase($this->mockRepositoryFactory);
    }

    public function testNotFoundOrder(): void
    {
        $this->mockRepositoryFactory->expectOrderRepositoryFindById(1, null);

        $this->expectException(EntityNotFoundException::class);

        $orderProcessedDto = new OrderProcessorDto(1);
        $this->useCase->execute($orderProcessedDto);
    }

    public function testFoundOrder(): void
    {
        $mockOrder = $this->createMock(Order::class);

        $this->mockRepositoryFactory->expectOrderRepositoryFindById(1, $mockOrder);

        $orderProcessedDto = new OrderProcessorDto(1);

        self::assertTrue($mockOrder === $this->useCase->execute($orderProcessedDto));
    }
}
