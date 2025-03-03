<?php

declare(strict_types=1);

namespace App\Application\Order\Service;

use App\Application\Client\UseCase\SendNotificationToClientUseCase;
use App\Application\Order\DTO\OrderProcessedDTO;
use App\Domain\Shared\Interface\LoggerInterface;

class NotificateOrderResultService
{

    public function __construct(
        private SendNotificationToClientUseCase $sendNotificationToClientUseCase,
        private LoggerInterface $logger
    ) {}

    public function execute(OrderProcessedDTO $orderProcessedDTO)
    {
        $this->sendNotificationToClientUseCase->execute($orderProcessedDTO);
    }
}
