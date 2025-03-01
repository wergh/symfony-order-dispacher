<?php

namespace App\Application\Order\DTO;

class OrderProcessedDTO
{

    public int $orderId;

    public string $message;

    public function __construct(int $orderId, string $message)
    {
        $this->orderId = $orderId;
        $this->message = $message;
    }

}
