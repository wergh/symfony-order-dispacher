<?php

declare(strict_types=1);

namespace App\Application\Order\UseCase;

use App\Application\Order\DTO\OrderProcessorDTO;
use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

final class GetOrderToProcessUseCase
{

    public function __construct(private RepositoryFactoryInterface $repositoryFactory) {}

    public function execute(OrderProcessorDTO $orderProcessorDTO): ?Order
    {
        $orderRepository = $this->repositoryFactory->createOrderRepository();

        $order  = $orderRepository->findById($orderProcessorDTO->orderId);
        if (null === $order) {
            throw new EntityNotFoundException('Order not found');
        }

        return $order;
    }
}
