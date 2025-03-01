<?php

declare(strict_types=1);

namespace App\Application\Order\Command;

use App\Application\Order\Dto\OrderDTO;
use App\Application\Order\UseCase\CreateOrderUseCase;
use App\Domain\Order\Entity\Order;

final class CreateOrderCommandHandler
{
    public function __construct(
        private CreateOrderUseCase $createOrderUseCase
    ) {
    }

    public function handle(OrderDTO $command): Order
    {
        return $this->createOrderUseCase->execute($command);
    }
}
