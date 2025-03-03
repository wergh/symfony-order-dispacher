<?php

declare(strict_types=1);

namespace App\Domain\Shared\Event;

use Carbon\Carbon;

/**
 * Interface for Domain Events.
 *
 * This interface defines the contract for domain events, which are used to represent
 * significant occurrences or state changes in the domain model. These events are typically
 * dispatched or published to notify other parts of the system or trigger side effects.
 */
interface DomainEventInterface
{
    /**
     * Returns the timestamp when the event occurred.
     *
     * @return Carbon The timestamp representing when the event occurred.
     */
    public function getOccurredOn(): Carbon;
}
