<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Listener;

use App\Application\Order\DTO\OrderProcessorDto;
use App\Application\Order\Service\FailedOrderService;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\MonitoringInterface;
use Exception;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

/**
 * Event listener for handling failed message events in the message bus.
 *
 * This class listens for WorkerMessageFailedEvent events and processes
 * failed order messages by moving them to a failed orders queue.
 */
class FailedMessageListener
{

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger Logger for recording failure information
     * @param MonitoringInterface $monitoring Monitoring service for tracking exceptions
     * @param FailedOrderService $failedOrderService Service for handling failed orders
     */
    public function __construct(
        private LoggerInterface     $logger,
        private MonitoringInterface $monitoring,
        private FailedOrderService  $failedOrderService)
    {
    }

    /**
     * Handles the WorkerMessageFailedEvent.
     *
     * If the message will not be retried, captures the exception in the monitoring system,
     * logs the failure, and moves the order to the failed orders queue using FailedOrderService.
     *
     * @param WorkerMessageFailedEvent $event The failed message event to process
     * @return void
     * @throws Exception When failed order processing fails
     */
    #[AsEventListener(event: WorkerMessageFailedEvent::class)]
    public function onMessageFailedEvent(WorkerMessageFailedEvent $event): void
    {
        $envelope = $event->getEnvelope();
        $exception = $event->getThrowable();
        $message = $envelope->getMessage();

        if (!$event->willRetry()) {
            $this->monitoring->captureException($exception);
            $orderProcessorDTO = new OrderProcessorDto($message->getOrderId());
            $this->logger->error(sprintf('Orden #%d fallida y movida a la cola de failed_orders ', $message->getOrderId()));
            try {
                $this->failedOrderService->execute($orderProcessorDTO);
            } catch (Exception $e) {
                $this->monitoring->captureException($e);
                throw new Exception($e->getMessage(), 0, $e);
            }
        }
    }
}
