<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Consumer;

use App\Application\Order\DTO\OrderProcessedDTO;
use App\Application\Order\Service\NotificateOrderResultService;
use App\Domain\Order\Event\OrderStatusUpdatedEvent;
use App\Domain\Shared\Logger\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class OrderStatusUpdatedConsumer
{
    public function __construct(
        private NotificateOrderResultService $notificateOrderResultService,
        private LoggerInterface $logger
    ) {}

    public function __invoke(OrderStatusUpdatedEvent $event): void
    {
        $this->logger->info(sprintf(
            'Procesando actualización de estado de orden: #%d',
            $event->getOrderId()
        ));

        $orderProcessedDTO = new OrderProcessedDTO(
            $event->getOrderId(),
            $event->getMessage()
        );

        try {
            $this->notificateOrderResultService->execute($orderProcessedDTO);
            $this->logger->info(sprintf('Notificación enviada para la orden #%d', $event->getOrderId()));
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                'Error al notificar el resultado de la orden #%d: %s',
                $event->getOrderId(),
                $e->getMessage()
            ));
        }
    }
}
