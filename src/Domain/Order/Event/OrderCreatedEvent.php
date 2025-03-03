<?php

declare(strict_types=1);

namespace App\Domain\Order\Event;

use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Event\DomainEventInterface;
use Carbon\Carbon;

/**
 * Represents the event triggered when an order is created.
 *
 * This event holds the necessary information about the order creation,
 * including the order ID, client ID, and the timestamp of when the event occurred.
 */
final class OrderCreatedEvent implements DomainEventInterface
{
    private Carbon $occurredOn;

    /**
     * Constructor to initialize the OrderCreatedEvent.
     *
     * @param int    $orderId   The ID of the order.
     * @param int    $clientId  The ID of the client associated with the order.
     * @param Carbon $createdAt The timestamp when the order was created.
     */
    public function __construct(
        private readonly int    $orderId,
        private readonly int    $clientId,
        private readonly Carbon $createdAt
    )
    {
        $this->occurredOn = Carbon::now();
    }

    /**
     * Factory method to create an instance of OrderCreatedEvent from an Order entity.
     *
     * @param Order $order The order entity from which to extract event data.
     *
     * @return self The created OrderCreatedEvent.
     */
    public static function fromOrder(Order $order): self
    {
        return new self(
            $order->getId(),
            $order->getClient()->getId(),
            $order->getCreatedAt()
        );
    }

    /**
     * Get the timestamp when the order was created.
     *
     * @return Carbon The created timestamp.
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * Get the ID of the order associated with this event.
     *
     * @return int The ID of the order.
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * Get the ID of the client who placed the order.
     *
     * @return int The ID of the client.
     */
    public function getClientId(): int
    {
        return $this->clientId;
    }

    /**
     * Get the timestamp when the event occurred.
     *
     * @return Carbon The event occurrence timestamp.
     */
    public function getOccurredOn(): Carbon
    {
        return $this->occurredOn;
    }
}
