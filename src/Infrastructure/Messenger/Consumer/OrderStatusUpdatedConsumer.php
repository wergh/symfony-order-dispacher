<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Consumer;

use App\Application\Order\DTO\OrderProcessedDto;
use App\Application\Order\Service\NotificateOrderResultService;
use App\Domain\Order\Event\OrderStatusUpdatedEvent;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\MonitoringInterface;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Consumer class for processing OrderStatusUpdatedEvent messages.
 *
 * This class handles the asynchronous processing of order status update events
 * via Symfony Messenger component and sends notifications about order results.
 */
#[AsMessageHandler]
class OrderStatusUpdatedConsumer
{
    /**
     * Monitoring service instance.
     *
     * @var MonitoringInterface
     */
    private MonitoringInterface $monitoring;

    /**
     * Constructor.
     *
     * @param NotificateOrderResultService $notificateOrderResultService Service for sending order result notifications
     * @param LoggerInterface $logger Logger for recording processing information
     * @param MonitoringInterface $monitoring Monitoring service for tracking exceptions
     */
    public function __construct(
        private NotificateOrderResultService $notificateOrderResultService,
        private LoggerInterface              $logger,
        MonitoringInterface                  $monitoring
    )
    {
        $this->monitoring = $monitoring;
    }

    /**
     * Handles the OrderStatusUpdatedEvent message.
     *
     * Processes an order status update event by sending notifications
     * through the NotificateOrderResultService. Logs processing information
     * and captures exceptions in the monitoring system.
     *
     * @param OrderStatusUpdatedEvent $event The order status updated event to process
     * @return void
     * @throws Exception When notification sending fails
     */
    public function __invoke(OrderStatusUpdatedEvent $event): void
    {
        $this->logger->info(sprintf(
            'Procesando actualización de estado de orden: #%d',
            $event->getOrderId()
        ));

        $orderProcessedDTO = new OrderProcessedDto(
            $event->getOrderId(),
            $event->getMessage()
        );

        try {
            $this->notificateOrderResultService->execute($orderProcessedDTO);
            $this->logger->info(sprintf('Notificación enviada para la orden #%d', $event->getOrderId()));
        } catch (Exception $e) {
            $this->logger->error(sprintf(
                'Error al notificar el resultado de la orden #%d: %s',
                $event->getOrderId(),
                $e->getMessage()
            ));
            $this->monitoring->captureException($e);
            throw new Exception($e->getMessage(), 0, $e);
        }
    }
}
