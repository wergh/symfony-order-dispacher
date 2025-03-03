<?php

namespace App\Application\Order\Service;

use App\Application\Order\DTO\OrderProcessorDto;
use App\Application\Order\UseCase\FailedOrderUseCase;
use App\Application\Order\UseCase\GetOrderToProcessUseCase;
use App\Domain\Shared\Exception\EntityNotFoundException;
use Exception;

/**
 * Service class for handling failed orders.
 */
class FailedOrderService
{
    /**
     * FailedOrderService constructor.
     *
     * @param FailedOrderUseCase $failedOrderUseCase The use case to handle failed orders.
     * @param GetOrderToProcessUseCase $getOrderToProcessUseCase The use case to fetch the order to be processed.
     */
    public function __construct(
        private FailedOrderUseCase $failedOrderUseCase,
        private GetOrderToProcessUseCase $getOrderToProcessUseCase,
    )
    {
    }

    /**
     * Executes the failed order process.
     *
     * @param OrderProcessorDto $orderProcessorDto The DTO containing the order ID to be processed.
     * @throws Exception If the order is not found or any other exception occurs during the processing.
     */
    public function execute(OrderProcessorDto $orderProcessorDto): void
    {
        try {
            $order = $this->getOrderToProcessUseCase->execute($orderProcessorDto);
            $this->failedOrderUseCase->execute($order);
        } catch (EntityNotFoundException $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }
    }
}
