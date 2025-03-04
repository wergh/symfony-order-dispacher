<?php

declare(strict_types=1);

namespace App\Application\Order\Command;

use App\Application\Order\Dto\CreateOrderDto;
use App\Application\Order\UseCase\CreateOrderUseCase;
use App\Domain\Order\Entity\Order;

/**
 * Command handler for creating an order.
 */
class CreateOrderCommandHandler
{
    /**
     * CreateOrderCommandHandler constructor.
     *
     * @param CreateOrderUseCase $createOrderUseCase The use case to create an order.
     */
    public function __construct(
        private CreateOrderUseCase $createOrderUseCase
    )
    {
    }

    /**
     * Handles the creation of an order.
     *
     * @param CreateOrderDto $command The DTO containing the order data to be created.
     *
     * @return Order The created order entity.
     */
    public function handle(CreateOrderDto $command): Order
    {
        return $this->createOrderUseCase->execute($command);
    }
}
