<?php

declare(strict_types=1);

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;
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
        $orderRepository = $this->repositoryFactory->createOrderRepository();
        $order->markAsAccepted();
        $orderRepository->save($order);
    }
}
