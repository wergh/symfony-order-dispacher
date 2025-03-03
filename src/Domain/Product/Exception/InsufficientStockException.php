<?php

declare(strict_types=1);

namespace App\Domain\Product\Exception;

use Exception;
use Throwable;

/**
 * Exception thrown when there is insufficient stock for a product.
 *
 * This exception is used to signal that the requested quantity for a product exceeds
 * the available stock. It can be thrown when attempting to decrease stock or fulfill
 * an order with insufficient inventory.
 */
class InsufficientStockException extends Exception
{
    /**
     * InsufficientStockException constructor.
     *
     * @param string $message The error message.
     * @param int $code The error code (optional).
     * @param Throwable|null $previous A previous exception (optional).
     */
    public function __construct(
        string $message = "Insufficient stock for the product.",
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
