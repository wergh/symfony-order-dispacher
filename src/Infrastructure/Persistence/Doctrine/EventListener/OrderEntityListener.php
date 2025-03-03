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

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Order::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Order::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Order::class)]
class OrderEntityListener
{

    private ?OrderStatusEnum $previousStatus = null;
    private LoggerInterface $logger;
    private MessageBusInterface $bus;
    private MonitoringInterface $monitoring;

    public function __construct(MessageBusInterface $bus, LoggerInterface $logger, MonitoringInterface $monitoring)
    {
        $this->bus = $bus;
        $this->logger = $logger;
        $this->monitoring = $monitoring;
    }

    public function postPersist(Order $order, PostPersistEventArgs $event): void
    {
        $event = OrderCreatedEvent::fromOrder($order);
        try {
            $this->bus->dispatch($event);
        } catch (ExceptionInterface $e) {
            $this->monitoring->captureException($e);
        }

    }

    public function preUpdate(Order $order, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('status')) {
            $this->previousStatus = OrderStatusEnum::from($event->getOldValue('status'));
        }
    }

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
