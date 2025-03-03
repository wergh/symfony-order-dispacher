<?php

declare(strict_types=1);

namespace App\Domain\Order\Event;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatusEnum;
use App\Domain\Shared\Event\DomainEventInterface;
use Carbon\Carbon;
use LogicException;

final class OrderStatusUpdatedEvent implements DomainEventInterface
{
    private Carbon $occurredOn;

    public function __construct(
        private readonly int    $orderId,
        private readonly string $message
    )
    {
        $this->occurredOn = Carbon::now();
    }

    public static function fromOrder(Order $order): self
    {
        $message = match ($order->getStatus()) {
            OrderStatusEnum::APPROVED => 'Tu pedido ha sido aceptado y está en proceso.',
            OrderStatusEnum::REJECTED => 'Lo sentimos, tu pedido ha sido rechazado.',
            OrderStatusEnum::FAILED => 'Lo sentimos, tu pedido ha fallado por un error inesperado y por lo tanto se ha cancelado. Vuelva a intentarlo de nuevo',
            default => throw new LogicException('Este evento solo debe dispararse cuando el pedido se acepta o rechaza.')
        };

        return new self($order->getId(), $message);
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOccurredOn(): Carbon
    {
        return $this->occurredOn;
    }
}
