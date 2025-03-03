<?php

namespace App\Application\Order\Service;

use App\Application\Order\DTO\OrderProcessorDto;
use App\Application\Order\UseCase\FailedOrderUseCase;
use App\Application\Order\UseCase\GetOrderToProcessUseCase;
use App\Domain\Shared\Exception\EntityNotFoundException;

class FailedOrderService
{
    public function __construct(
        private FailedOrderUseCase $failedOrderUseCase,
        private GetOrderToProcessUseCase $getOrderToProcessUseCase,
    )
    {
    }

    public function execute(OrderProcessorDto $orderProcessorDto)
    {
        try {
            $order = $this->getOrderToProcessUseCase->execute($orderProcessorDto);
            $this->failedOrderUseCase->execute($order);
        } catch (EntityNotFoundException $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }

    }
}
