<?php

declare(strict_types=1);

namespace App\Application\Order\Service;

use App\Application\Client\UseCase\SendNotificationToClientUseCase;
use App\Application\Order\DTO\OrderProcessedDto;
use App\Domain\Shared\Exception\EntityNotFoundException;

/**
 * Service class for notifying the client about the order result.
 */
class NotificateOrderResultService
{
    /**
     * NotificateOrderResultService constructor.
     *
     * @param SendNotificationToClientUseCase $sendNotificationToClientUseCase The use case to send notifications to the client.
     */
    public function __construct(
        private SendNotificationToClientUseCase $sendNotificationToClientUseCase,
    )
    {
    }

    /**
     * Executes the process of notifying the client about the order result.
     *
     * @param OrderProcessedDto $orderProcessedDTO The DTO containing order processing information.
     * @throws EntityNotFoundException
     */
    public function execute(OrderProcessedDto $orderProcessedDTO): void
    {
        $this->sendNotificationToClientUseCase->execute($orderProcessedDTO);
    }
}
