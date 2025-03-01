<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Domain\Shared\Event\DomainEventInterface;

interface EventBusInterface
{
    public function dispatch(DomainEventInterface $event): void;
}
