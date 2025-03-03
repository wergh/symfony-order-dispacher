<?php

declare(strict_types=1);

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Use case for marking an order as failed.
 */
final class FailedOrderUseCase
{
    /**
     * FailedOrderUseCase constructor.
     *
     * @param RepositoryFactoryInterface $repositoryFactory The factory to create repositories for domain entities.
     * @param LoggerInterface $logger The logger for logging any necessary messages.
     */
    public function __construct(private RepositoryFactoryInterface $repositoryFactory, private LoggerInterface $logger)
    {
    }

    /**
     * Executes the failure process for an order.
     *
     * @param Order $order The order to be marked as failed.
     *
     * @return void
     */
    public function execute(Order $order): void
    {
        $orderRepository = $this->repositoryFactory->createOrderRepository();
        $order->markAsFailed();
        $orderRepository->save($order);
    }
}
