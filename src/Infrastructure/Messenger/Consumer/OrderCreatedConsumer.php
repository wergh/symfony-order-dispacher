<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Consumer;

use App\Application\Order\DTO\OrderProcessorDto;
use App\Application\Order\Service\OrderProcessorService;
use App\Domain\Order\Event\OrderCreatedEvent;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\MonitoringInterface;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Consumer class for processing OrderCreatedEvent messages.
 *
 * This class handles the asynchronous processing of order creation events
 * via Symfony Messenger component.
 */
#[AsMessageHandler]
class OrderCreatedConsumer
{

    /**
     * Constructor.
     *
     * @param OrderProcessorService $orderProcessorService Service for processing orders
     * @param LoggerInterface $logger Logger for recording processing information
     * @param MonitoringInterface $monitoring Monitoring service for tracking exceptions
     */
    public function __construct(
        private OrderProcessorService $orderProcessorService,
        private LoggerInterface       $logger,
        private MonitoringInterface   $monitoring
    )
    {
        $this->monitoring = $monitoring;
    }

    /**
     * Handles the OrderCreatedEvent message.
     *
     * Processes a newly created order by delegating to the OrderProcessorService.
     * Logs processing information and captures exceptions in the monitoring system.
     *
     * @param OrderCreatedEvent $event The order created event to process
     * @return void
     * @throws Exception When order processing fails
     */
    public function __invoke(OrderCreatedEvent $event): void
    {
        $this->logger->info(sprintf(
            'Procesando orden creada: #%d para cliente #%d',
            $event->getOrderId(),
            $event->getClientId()
        ));

        $orderProcessorDTO = new OrderProcessorDto($event->getOrderId());

        try {
            $this->orderProcessorService->execute($orderProcessorDTO);
            $this->logger->info(sprintf('Orden #%d procesada correctamente', $event->getOrderId()));
        } catch (Exception $e) {
            $this->logger->error(sprintf(
                'Error al procesar la orden #%d: %s',
                $event->getOrderId(),
                $e->getMessage()
            ));
            $this->monitoring->captureException($e);
            throw new Exception($e->getMessage(), 0, $e);
        }
    }
}
