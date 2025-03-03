<?php

declare(strict_types=1);

namespace App\Domain\Order\Enum;

/**
 * Enum representing the possible statuses of an order.
 *
 * This enum is used to track the current status of an order in the system.
 * It includes four possible states: pending, approved, rejected, and failed.
 */
enum OrderStatusEnum: string
{
    /**
     * The order is in the pending state.
     */
    case PENDING = 'pending';

    /**
     * The order has been approved.
     */
    case APPROVED = 'approved';

    /**
     * The order has been rejected.
     */
    case REJECTED = 'rejected';

    /**
     * The order has failed.
     */
    case FAILED = 'failed';
}
