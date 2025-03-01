<?php

declare(strict_types=1);

namespace App\Application\Order\Sequencer;

use App\Application\Order\DTO\OrderProcessorDTO;
use App\Application\Order\UseCase\AcceptOrderUseCase;
use App\Application\Order\UseCase\GetOrderToProcessUseCase;
use App\Application\Order\UseCase\RejectOrderUseCase;
use App\Application\Order\UseCase\UpdateStocksUseCase;
use App\Application\Order\UseCase\ValidateStockUseCase;
use App\Domain\Product\Exception\InsufficientStockException;
use App\Domain\Shared\Exception\EntityNotFoundException;
use Exception;

class OrderProcessingSequencer
{
    public function __construct(
        private GetOrderToProcessUseCase $getOrderToProcessUseCase,
        private ValidateStockUseCase $validateStockUseCase,
        private UpdateStocksUseCase $updateStocksUseCase,
        private AcceptOrderUseCase $acceptOrderUseCase,
        private RejectOrderUseCase $rejectOrderUseCase
    ) {}

    public function process(OrderProcessorDTO $orderDto): void
    {

        try {
            $order = $this->getOrderToProcessUseCase->execute($orderDto);
            $this->validateStockUseCase->execute($order);
            $this->updateStocksUseCase->execute($order);
            $this->acceptOrderUseCase->execute($order);
        } catch (InsufficientStockException $e) {
            $this->rejectOrderUseCase->execute($order);
            throw new Exception($e->getMessage());
        } catch (EntityNotFoundException $e) {
            throw new Exception($e->getMessage());
        }
    }
}
