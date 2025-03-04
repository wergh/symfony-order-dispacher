<?php

namespace App\Tests\Infrastructure\Messenger\Consumer;

use App\Application\Order\DTO\OrderProcessorDto;
use App\Application\Order\Service\OrderProcessorService;
use App\Domain\Order\Event\OrderCreatedEvent;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\MonitoringInterface;
use App\Infrastructure\Messenger\Consumer\OrderCreatedConsumer;
use Carbon\Carbon;
use Exception;
use PHPUnit\Framework\TestCase;

class OrderCreatedConsumerTest extends TestCase
{
    private OrderProcessorService $orderProcessorService;
    private LoggerInterface $logger;
    private MonitoringInterface $monitoring;
    private OrderCreatedConsumer $consumer;

    protected function setUp(): void
    {
        $this->orderProcessorService = $this->createMock(OrderProcessorService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->monitoring = $this->createMock(MonitoringInterface::class);

        $this->consumer = new OrderCreatedConsumer(
            $this->orderProcessorService,
            $this->logger,
            $this->monitoring
        );
    }

    public function testInvokeSuccessfulProcessing(): void
    {
        $event = new OrderCreatedEvent(1, 2, Carbon::now()); // orderId, clientId

        $matcher = $this->exactly(2);
        $this->logger->expects($matcher)
            ->method('info')
            ->willReturnCallback(function () use ($matcher){
                if ($matcher->getInvocationCount() === 1) {
                    $this->stringContains('Procesando orden creada: #1');
                }
                if ($matcher->getInvocationCount() === 2) {
                    $this->stringContains('Orden #1 procesada correctamente');
                }
            });

        $this->orderProcessorService->expects($this->once())
            ->method('execute')
            ->with($this->callback(function(OrderProcessorDto $dto) {
                return $dto->orderId === 1;
            }));

        $this->consumer->__invoke($event);
    }

    public function testInvokeWithException(): void
    {
        // Arrange
        $event = new OrderCreatedEvent(1, 2, Carbon::now());
        $exception = new Exception('Error de procesamiento');

        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Procesando orden creada: #1'));

        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Error al procesar la orden #1'));

        $this->orderProcessorService->expects($this->once())
            ->method('execute')
            ->willThrowException($exception);

        $this->monitoring->expects($this->once())
            ->method('captureException')
            ->with($exception);

        $this->expectException(Exception::class);
        $this->consumer->__invoke($event);
    }
}
