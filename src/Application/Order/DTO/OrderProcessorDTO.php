<?php

declare(strict_types=1);

namespace App\Application\Order\DTO;

final class OrderProcessorDTO
{

    public int $orderId;


    /**
     * @param int $orderId
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }
}
