<?php

declare(strict_types=1);

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

/**
 * Use case for rejecting an order.
 */
final class RejectOrderUseCase
{
    /**
     * RejectOrderUseCase constructor.
     *
     * @param RepositoryFactoryInterface $repositoryFactory The factory to create repositories for domain entities.
     */
    public function __construct(private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    /**
     * Executes the rejection of the order.
     *
     * @param Order $order The order to be rejected.
     *
     * @return void
     */
    public function execute(Order $order): void
    {
        $orderRepository = $this->repositoryFactory->createOrderRepository();
        $order->markAsRejected();
        $orderRepository->save($order);
    }
}
