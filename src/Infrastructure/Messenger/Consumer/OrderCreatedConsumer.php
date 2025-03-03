<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Consumer;

use App\Application\Order\DTO\OrderProcessorDTO;
use App\Application\Order\Service\OrderProcessorService;
use App\Domain\Order\Event\OrderCreatedEvent;
use App\Domain\Shared\Interface\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class OrderCreatedConsumer
{
    public function __construct(
        private OrderProcessorService $orderProcessorService,
        private LoggerInterface $logger
    ) {}

    public function __invoke(OrderCreatedEvent $event): void
    {
        $this->logger->info(sprintf(
            'Procesando orden creada: #%d para cliente #%d',
            $event->getOrderId(),
            $event->getClientId()
        ));

        $orderProcessorDTO = new OrderProcessorDTO($event->getOrderId());

        try {
            $this->orderProcessorService->execute($orderProcessorDTO);
            $this->logger->info(sprintf('Orden #%d procesada correctamente', $event->getOrderId()));
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                'Error al procesar la orden #%d: %s',
                $event->getOrderId(),
                $e->getMessage()
            ));
        }
    }
}
