<?php

declare(strict_types=1);

namespace App\Application\Client\UseCase;

use App\Application\Order\DTO\OrderProcessedDto;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

final class SendNotificationToClientUseCase
{

    public function __construct(private LoggerInterface $logger, private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    public function execute(OrderProcessedDto $orderProcessedDTO)
    {
        $orderRepository = $this->repositoryFactory->createOrderRepository();

        $order = $orderRepository->findById($orderProcessedDTO->orderId);
        if ($order === null) {
            throw new EntityNotFoundException('Order not found');
        }

        /** Aquí en vez del logger se podría despachar el evento de enviar un email */
        if ($order->isAccepted()) {
            $this->logger->info('El pedido ha finalizado con existo para el cliente ' . $order->getClient()->getName());
        } else {
            $this->logger->error('El pedido no ha sido denegado debido a: ' . $orderProcessedDTO->message);
        }

    }

}
