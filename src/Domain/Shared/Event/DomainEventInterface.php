<?php

declare(strict_types=1);

namespace App\Domain\Shared\Event;

use Carbon\Carbon;

interface DomainEventInterface
{
    public function getOccurredOn(): Carbon;
}
