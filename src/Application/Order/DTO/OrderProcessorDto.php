<?php

declare(strict_types=1);

namespace App\Application\Order\DTO;

/**
 * Data Transfer Object (DTO) for representing the order processor data.
 */
final class OrderProcessorDto
{
    /**
     * @var int The ID of the order to be processed.
     */
    public int $orderId;

    /**
     * OrderProcessorDto constructor.
     *
     * @param int $orderId The ID of the order to be processed.
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }
}
