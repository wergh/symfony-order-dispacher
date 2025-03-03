<?php

declare(strict_types=1);

namespace App\Application\Client\UseCase;

use App\Application\Order\DTO\OrderProcessedDto;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

/**
 * Use case for sending a notification to the client after an order is processed.
 */
final class SendNotificationToClientUseCase
{
    /**
     * SendNotificationToClientUseCase constructor.
     *
     * @param LoggerInterface           $logger          Logger for logging notifications.
     * @param RepositoryFactoryInterface $repositoryFactory Factory for creating the order repository.
     */
    public function __construct(private LoggerInterface $logger, private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    /**
     * Executes the process of sending a notification to the client based on order status.
     *
     * @param OrderProcessedDto $orderProcessedDTO The DTO containing the processed order data.
     *
     * @throws EntityNotFoundException If the order is not found.
     */
    public function execute(OrderProcessedDto $orderProcessedDTO): void
    {
        $orderRepository = $this->repositoryFactory->createOrderRepository();

        $order = $orderRepository->findById($orderProcessedDTO->orderId);
        if ($order === null) {
            throw new EntityNotFoundException('Order not found');
        }

        /** Aquí en vez del logger se podría despachar el evento de enviar un email */
        if ($order->isAccepted()) {
            $this->logger->info('The order has been successfully completed for client ' . $order->getClient()->getName());
        } else {
            $this->logger->error('The order has been denied due to: ' . $orderProcessedDTO->message);
        }
    }
}
