<?php

declare(strict_types=1);

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatusEnum;
use App\Domain\Order\Event\OrderStatusUpdatedEvent;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;
use App\Domain\Shared\Logger\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class AcceptOrderUseCase
{
    private MessageBusInterface $bus;
    public function __construct(private RepositoryFactoryInterface $repositoryFactory, private LoggerInterface $logger, MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function execute(Order $order): void
    {
        $this->logger->info('Estoy dentro del Aceptador');
        $orderRepository = $this->repositoryFactory->createOrderRepository();
        $oldStatus = $order->getStatus();
        $order->markAsAccepted();
        $orderRepository->save($order);
        if ($oldStatus !== OrderStatusEnum::APPROVED) {
            $event = OrderStatusUpdatedEvent::fromOrder($order);
            $this->bus->dispatch($event);
        }
        $this->logger->info('Marco como aceptado y salgo del Aceptador');
    }
}
