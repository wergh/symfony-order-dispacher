<?php

namespace App\Tests\Infrastructure\Messenger\Listener;

use App\Application\Order\DTO\OrderProcessorDto;
use App\Application\Order\Service\FailedOrderService;
use App\Domain\Order\Event\OrderCreatedEvent;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\MonitoringInterface;
use App\Infrastructure\Messenger\Listener\FailedMessageListener;
use Carbon\Carbon;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

class FailedMessageListenerTest extends TestCase
{
    private LoggerInterface $logger;
    private MonitoringInterface $monitoring;
    private FailedOrderService $failedOrderService;
    private FailedMessageListener $listener;

    protected function setUp(): void
    {
        \DG\BypassFinals::enable();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->monitoring = $this->createMock(MonitoringInterface::class);
        $this->failedOrderService = $this->createMock(FailedOrderService::class);

        $this->listener = new FailedMessageListener(
            $this->logger,
            $this->monitoring,
            $this->failedOrderService
        );
    }

    public function testOnMessageFailedEventWithRetry(): void
    {
        // Arrange
        $message = new OrderCreatedEvent(1, 2, Carbon::now());
        $envelope = new Envelope($message);
        $exception = new Exception('Error de procesamiento');

        $event = $this->createMock(WorkerMessageFailedEvent::class);
        $event->method('getEnvelope')->willReturn($envelope);
        $event->method('getThrowable')->willReturn($exception);
        $event->method('willRetry')->willReturn(true);

        // No deberían llamarse estos métodos si willRetry es true
        $this->monitoring->expects($this->never())->method('captureException');
        $this->logger->expects($this->never())->method('error');
        $this->failedOrderService->expects($this->never())->method('execute');

        // Act
        $this->listener->onMessageFailedEvent($event);
    }

    public function testOnMessageFailedEventWithoutRetry(): void
    {
        // Arrange
        $message = new OrderCreatedEvent(1, 2, Carbon::now());
        $envelope = new Envelope($message);
        $exception = new Exception('Error de procesamiento');

        $event = $this->createMock(WorkerMessageFailedEvent::class);
        $event->method('getEnvelope')->willReturn($envelope);
        $event->method('getThrowable')->willReturn($exception);
        $event->method('willRetry')->willReturn(false);

        $this->monitoring->expects($this->once())
            ->method('captureException')
            ->with($exception);

        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Orden #1 fallida'));

        $this->failedOrderService->expects($this->once())
            ->method('execute')
            ->with($this->callback(function(OrderProcessorDto $dto) {
                return $dto->orderId === 1;
            }));

        // Act
        $this->listener->onMessageFailedEvent($event);
    }

    public function testOnMessageFailedEventWithServiceException(): void
    {
        // Arrange
        $message = new OrderCreatedEvent(1, 2, Carbon::now());
        $envelope = new Envelope($message);
        $exception = new Exception('Error de procesamiento');
        $serviceException = new Exception('Error en el servicio');

        $event = $this->createMock(WorkerMessageFailedEvent::class);
        $event->method('getEnvelope')->willReturn($envelope);
        $event->method('getThrowable')->willReturn($exception);
        $event->method('willRetry')->willReturn(false);

        $this->monitoring->expects($this->exactly(2))
            ->method('captureException')
            ->withConsecutive([$exception], [$serviceException]);

        $this->failedOrderService->expects($this->once())
            ->method('execute')
            ->willThrowException($serviceException);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->listener->onMessageFailedEvent($event);
    }
}
