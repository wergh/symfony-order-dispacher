<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\EventListener;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatusEnum;
use App\Domain\Order\Event\OrderCreatedEvent;
use App\Domain\Order\Event\OrderStatusUpdatedEvent;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\MonitoringInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Entity listener for Order entities.
 *
 * This class listens for Doctrine lifecycle events on Order entities and
 * dispatches domain events when orders are created or their status is updated.
 */
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Order::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Order::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Order::class)]
class OrderEntityListener
{
    /**
     * Stores the previous status of an order before update.
     *
     * @var OrderStatusEnum|null
     */
    private ?OrderStatusEnum $previousStatus = null;

    /**
     * The logger service.
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * The message bus for dispatching events.
     *
     * @var MessageBusInterface
     */
    private MessageBusInterface $bus;

    /**
     * The monitoring service.
     *
     * @var MonitoringInterface
     */
    private MonitoringInterface $monitoring;

    /**
     * Constructor.
     *
     * @param MessageBusInterface $bus The message bus for dispatching events
     * @param LoggerInterface $logger The logger service
     * @param MonitoringInterface $monitoring The monitoring service
     */
    public function __construct(MessageBusInterface $bus, LoggerInterface $logger, MonitoringInterface $monitoring)
    {
        $this->bus = $bus;
        $this->logger = $logger;
        $this->monitoring = $monitoring;
    }

    /**
     * Handles post-persist events for Order entities.
     *
     * Dispatches an OrderCreatedEvent when a new order is persisted.
     *
     * @param Order $order The newly persisted order
     * @param PostPersistEventArgs $event The post-persist event arguments
     * @return void
     */
    public function postPersist(Order $order, PostPersistEventArgs $event): void
    {
        $event = OrderCreatedEvent::fromOrder($order);
        try {
            $this->bus->dispatch($event);
        } catch (ExceptionInterface $e) {
            $this->monitoring->captureException($e);
        }

    }

    /**
     * Handles pre-update events for Order entities.
     *
     * Stores the previous status of an order if it's being changed.
     *
     * @param Order $order The order being updated
     * @param PreUpdateEventArgs $event The pre-update event arguments
     * @return void
     */
    public function preUpdate(Order $order, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('status')) {
            $this->previousStatus = OrderStatusEnum::from($event->getOldValue('status'));
        }
    }

    /**
     * Handles post-update events for Order entities.
     *
     * Dispatches an OrderStatusUpdatedEvent when an order's status has changed.
     *
     * @param Order $order The updated order
     * @param PostUpdateEventArgs $event The post-update event arguments
     * @return void
     */
    public function postUpdate(Order $order, PostUpdateEventArgs $event): void
    {
        if ($this->previousStatus !== null) {
            if ($this->previousStatus != $order->getStatus()) {
                $event = OrderStatusUpdatedEvent::fromOrder($order);
                try {
                    $this->bus->dispatch($event);
                } catch (ExceptionInterface $e) {
                    $this->monitoring->captureException($e);
                }
            }
        }

    }

}
