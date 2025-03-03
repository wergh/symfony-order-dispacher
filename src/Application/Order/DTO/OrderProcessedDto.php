<?php

declare(strict_types=1);

namespace App\Application\Order\DTO;

final class OrderProcessedDto
{

    public int $orderId;

    public string $message;

    public function __construct(int $orderId, string $message)
    {
        $this->orderId = $orderId;
        $this->message = $message;
    }

}
