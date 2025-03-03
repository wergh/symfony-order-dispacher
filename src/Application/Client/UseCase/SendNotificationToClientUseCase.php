<?php

declare(strict_types=1);

namespace App\Application\Client\UseCase;

use App\Application\Order\DTO\OrderProcessedDTO;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;
use App\Domain\Shared\Interface\LoggerInterface;

final class SendNotificationToClientUseCase
{

    public function __construct(private LoggerInterface $logger, private RepositoryFactoryInterface $repositoryFactory) {}

    public function execute(OrderProcessedDTO $orderProcessedDTO)
    {
        $orderRepository = $this->repositoryFactory->createOrderRepository();

        $order = $orderRepository->findById($orderProcessedDTO->orderId);
        if ($order === null) {
            throw new EntityNotFoundException('Order not found');
        }


        if($order->isAccepted()) {
            $this->logger->info('El pedido ha finalizado con existo para el cliente '. $order->getClient()->getName());
        } else {
            $this->logger->error('El pedido no ha sido denegado debido a: '.$orderProcessedDTO->message);
        }

    }

}
