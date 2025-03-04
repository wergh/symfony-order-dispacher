<?php

namespace App\Tests\Infrastructure\Messenger\Consumer;

use App\Application\Order\DTO\OrderProcessedDto;
use App\Application\Order\Service\NotificateOrderResultService;
use App\Domain\Order\Event\OrderStatusUpdatedEvent;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\MonitoringInterface;
use App\Infrastructure\Messenger\Consumer\OrderStatusUpdatedConsumer;
use Exception;
use PHPUnit\Framework\TestCase;

class OrderStatusUpdatedConsumerTest extends TestCase
{
    private NotificateOrderResultService $notificateOrderResultService;
    private LoggerInterface $logger;
    private MonitoringInterface $monitoring;
    private OrderStatusUpdatedConsumer $consumer;

    protected function setUp(): void
    {
        $this->notificateOrderResultService = $this->createMock(NotificateOrderResultService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->monitoring = $this->createMock(MonitoringInterface::class);

        $this->consumer = new OrderStatusUpdatedConsumer(
            $this->notificateOrderResultService,
            $this->logger,
            $this->monitoring
        );
    }

    public function testInvokeSuccessfulProcessing(): void
    {
        // Arrange
        $event = $this->createOrderStatusUpdatedEvent(1, 'Orden procesada correctamente');

        $matcher = $this->exactly(2);
        $this->logger->expects($matcher)
            ->method('info')
            ->willReturnCallback(function () use ($matcher){
                if ($matcher->getInvocationCount() === 1) {
                    $this->stringContains('Procesando actualización de estado de orden: #1');
                }
                if ($matcher->getInvocationCount() === 2) {
                    $this->stringContains('Notificación enviada para la orden #1');
                }
            });


        $this->notificateOrderResultService->expects($this->once())
            ->method('execute')
            ->with($this->callback(function(OrderProcessedDto $dto) {
                return $dto->orderId === 1 && $dto->message === 'Orden procesada correctamente';
            }));

        // Act
        $this->consumer->__invoke($event);
    }

    public function testInvokeWithException(): void
    {
        // Arrange
        $event = $this->createOrderStatusUpdatedEvent(1, 'Orden procesada correctamente');
        $exception = new Exception('Error de notificación');

        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Procesando actualización de estado de orden: #1'));

        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Error al notificar el resultado de la orden #1'));

        $this->notificateOrderResultService->expects($this->once())
            ->method('execute')
            ->willThrowException($exception);

        $this->monitoring->expects($this->once())
            ->method('captureException')
            ->with($exception);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->consumer->__invoke($event);
    }

    /**
     * Helper method to create an OrderStatusUpdatedEvent
     */
    private function createOrderStatusUpdatedEvent(int $orderId, string $message): OrderStatusUpdatedEvent
    {

        $event = new OrderStatusUpdatedEvent($orderId, $message);

        return $event;
    }
}
