<?php

declare(strict_types=1);

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class FailedOrderUseCase
{

    public function __construct(private RepositoryFactoryInterface $repositoryFactory, private LoggerInterface $logger)
    {
    }

    public function execute(Order $order): void
    {
        $orderRepository = $this->repositoryFactory->createOrderRepository();
        $order->markAsFailed();
        $orderRepository->save($order);
    }
}
