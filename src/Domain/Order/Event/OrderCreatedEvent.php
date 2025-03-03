<?php

declare(strict_types=1);

namespace App\Domain\Order\Event;

use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Event\DomainEventInterface;
use Carbon\Carbon;

final class OrderCreatedEvent implements DomainEventInterface
{
    private Carbon $occurredOn;

    public function __construct(
        private readonly int    $orderId,
        private readonly int    $clientId,
        private readonly Carbon $createdAt
    )
    {
        $this->occurredOn = Carbon::now();
    }

    public static function fromOrder(Order $order): self
    {
        return new self(
            $order->getId(),
            $order->getClient()->getId(),
            $order->getCreatedAt()
        );
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getOccurredOn(): Carbon
    {
        return $this->occurredOn;
    }
}
