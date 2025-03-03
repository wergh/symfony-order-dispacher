<?php

declare(strict_types=1);

namespace App\Application\Order\Sequencer;

use App\Application\Order\DTO\OrderProcessorDTO;
use App\Application\Order\UseCase\AcceptOrderUseCase;
use App\Application\Order\UseCase\GetOrderToProcessUseCase;
use App\Application\Order\UseCase\RejectOrderUseCase;
use App\Application\Order\UseCase\UpdateStocksUseCase;
use App\Application\Order\UseCase\ValidateStockUseCase;
use App\Domain\Order\Event\OrderStatusUpdatedEvent;
use App\Domain\Product\Exception\InsufficientStockException;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderProcessingSequencer
{
    private MessageBusInterface $bus;

    public function __construct(
        private GetOrderToProcessUseCase $getOrderToProcessUseCase,
        private ValidateStockUseCase $validateStockUseCase,
        private UpdateStocksUseCase $updateStocksUseCase,
        private AcceptOrderUseCase $acceptOrderUseCase,
        private RejectOrderUseCase $rejectOrderUseCase,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        MessageBusInterface $bus
    )
    {
        $this->bus = $bus;
    }

    public function process(OrderProcessorDTO $orderDto): void
    {
        $this->entityManager->beginTransaction();
        try {
            $order = $this->getOrderToProcessUseCase->execute($orderDto);
            $order->markAsProcessed();
            $this->validateStockUseCase->execute($order);
            $this->updateStocksUseCase->execute($order);
            $this->acceptOrderUseCase->execute($order);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (InsufficientStockException $e) {
            $this->entityManager->rollback();
            $order->markAsProcessed();
            $this->rejectOrderUseCase->execute($order);
        } catch (EntityNotFoundException $e) {
            $this->entityManager->rollback();
            throw new Exception($e->getMessage());
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->logger->error('Unexpected error: ' . $e->getMessage());
            throw $e;
        }
    }
}
