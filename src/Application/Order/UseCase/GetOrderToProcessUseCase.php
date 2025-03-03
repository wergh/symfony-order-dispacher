<?php

declare(strict_types=1);

namespace App\Application\Order\UseCase;

use App\Application\Order\DTO\OrderProcessorDto;
use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

/**
 * Use case for retrieving an order to process.
 */
final class GetOrderToProcessUseCase
{
    /**
     * GetOrderToProcessUseCase constructor.
     *
     * @param RepositoryFactoryInterface $repositoryFactory The factory to create repositories for domain entities.
     */
    public function __construct(private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    /**
     * Executes the retrieval of the order to process.
     *
     * @param OrderProcessorDto $orderProcessorDTO The DTO containing the order ID.
     *
     * @return Order|null The order if found, or null if not found.
     *
     * @throws EntityNotFoundException If the order is not found.
     */
    public function execute(OrderProcessorDto $orderProcessorDTO): ?Order
    {
        $orderRepository = $this->repositoryFactory->createOrderRepository();

        $order = $orderRepository->findById($orderProcessorDTO->orderId);
        if (null === $order) {
            throw new EntityNotFoundException('Order not found');
        }

        return $order;
    }
}
