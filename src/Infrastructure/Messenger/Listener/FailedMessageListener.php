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

class FailedMessageListener
{


    public function __construct(
        private LoggerInterface     $logger,
        private MonitoringInterface $monitoring,
        private FailedOrderService  $failedOrderService)
    {
    }

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
