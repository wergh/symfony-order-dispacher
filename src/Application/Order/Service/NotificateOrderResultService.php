<?php

declare(strict_types=1);

namespace App\Application\Order\Service;

use App\Application\Client\UseCase\SendNotificationToClientUseCase;
use App\Application\Order\DTO\OrderProcessedDto;
use App\Domain\Shared\Interface\LoggerInterface;

class NotificateOrderResultService
{

    public function __construct(
        private SendNotificationToClientUseCase $sendNotificationToClientUseCase,
    )
    {
    }

    public function execute(OrderProcessedDto $orderProcessedDTO)
    {
        $this->sendNotificationToClientUseCase->execute($orderProcessedDTO);
    }
}
