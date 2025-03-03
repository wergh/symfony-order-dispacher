<?php

declare(strict_types=1);

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

final class RejectOrderUseCase
{
    public function __construct(private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    public function execute(Order $order): void
    {
        $orderRepository = $this->repositoryFactory->createOrderRepository();
        $order->markAsRejected();
        $orderRepository->save($order);
    }
}
