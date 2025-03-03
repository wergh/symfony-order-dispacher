<?php

declare(strict_types=1);

namespace App\Application\Order\DTO;

/**
 * Data Transfer Object (DTO) for representing a processed order.
 */
final class OrderProcessedDto
{
    /**
     * @var int The ID of the processed order.
     */
    public int $orderId;

    /**
     * @var string The message associated with the processed order (e.g., success or failure).
     */
    public string $message;

    /**
     * OrderProcessedDto constructor.
     *
     * @param int $orderId The ID of the processed order.
     * @param string $message The message associated with the processed order.
     */
    public function __construct(int $orderId, string $message)
    {
        $this->orderId = $orderId;
        $this->message = $message;
    }
}
