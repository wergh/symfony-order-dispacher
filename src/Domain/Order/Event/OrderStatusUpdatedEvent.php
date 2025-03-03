<?php

declare(strict_types=1);

namespace App\Domain\Order\Event;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatusEnum;
use App\Domain\Shared\Event\DomainEventInterface;
use Carbon\Carbon;
use LogicException;

/**
 * Represents the event triggered when the status of an order is updated.
 *
 * This event contains the order ID and a message based on the updated status of the order.
 * The message provides feedback to the user regarding the outcome of their order.
 */
final class OrderStatusUpdatedEvent implements DomainEventInterface
{
    private Carbon $occurredOn;

    /**
     * Constructor to initialize the OrderStatusUpdatedEvent.
     *
     * @param int    $orderId The ID of the order whose status was updated.
     * @param string $message The message associated with the updated status.
     */
    public function __construct(
        private readonly int    $orderId,
        private readonly string $message
    )
    {
        $this->occurredOn = Carbon::now();
    }

    /**
     * Factory method to create an instance of OrderStatusUpdatedEvent from an Order entity.
     *
     * Based on the order's status, it generates an appropriate message.
     *
     * @param Order $order The order entity from which to extract the event data.
     *
     * @return self The created OrderStatusUpdatedEvent.
     *
     * @throws LogicException If the status of the order is not APPROVED, REJECTED, or FAILED.
     */
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
     * Get the message associated with the updated order status.
     *
     * @return string The message describing the status change.
     */
    public function getMessage(): string
    {
        return $this->message;
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
