<?php

declare(strict_types=1);

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

/**
 * Use case for accepting an order and saving it in the repository.
 */
final class AcceptOrderUseCase
{
    /**
     * AcceptOrderUseCase constructor.
     *
     * @param RepositoryFactoryInterface $repositoryFactory The factory to create repositories for domain entities.
     */
    public function __construct(private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    /**
     * Executes the process of accepting an order.
     *
     * @param Order $order The order to be accepted.
     */
    public function execute(Order $order): void
    {
        $orderRepository = $this->repositoryFactory->createOrderRepository();
        $order->markAsAccepted();
        $orderRepository->save($order);
    }
}
