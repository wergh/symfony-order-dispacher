<?php

declare(strict_types=1);

namespace App\Domain\Order\Enum;

enum OrderStatusEnum: string
{

    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

}
