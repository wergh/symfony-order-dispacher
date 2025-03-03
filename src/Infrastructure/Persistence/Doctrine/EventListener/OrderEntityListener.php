<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\EventListener;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatusEnum;
use App\Domain\Order\Event\OrderCreatedEvent;
use App\Domain\Order\Event\OrderStatusUpdatedEvent;
use App\Domain\Shared\Interface\LoggerInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Order::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Order::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Order::class)]
class OrderEntityListener
{

    private ?OrderStatusEnum $previousStatus = null;
    private LoggerInterface $logger;
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus, LoggerInterface $logger)
    {
        $this->bus = $bus;
        $this->logger = $logger;
    }

    public function postPersist(Order $order, PostPersistEventArgs $event): void
    {
        $this->logger->info(sprintf('ORDER CREADA Y PILLADA EN EL EVENTO ESTE created: %s', $order->getId()));
        $event = OrderCreatedEvent::fromOrder($order);
        $this->bus->dispatch($event);
    }

    public function preUpdate(Order $order, PreUpdateEventArgs $event): void
    {
        $this->logger->info("Entro en el preUpdate");
        if ($event->hasChangedField('status')) {
            $this->logger->info("Entro en el HasChanged");
            $this->previousStatus = OrderStatusEnum::from($event->getOldValue('status'));
        }
    }

    public function postUpdate(Order $order, PostUpdateEventArgs $event): void
    {
        $this->logger->info("Entro en el postUpdate");
        if ($this->previousStatus !== null) {
            if ($this->previousStatus === $order->getStatus()) {
                $this->logger->info("El status no ha cambiado ");
            }
            else {
                $this->logger->info("El status SI ha cambiado ");
                $this->logger->info(sprintf('ORDER ACTUALIZADA EN EL EVENTO ESTE created: %s', $order->getId()));
                $event = OrderStatusUpdatedEvent::fromOrder($order);
                $this->bus->dispatch($event);
            }
        }

    }

}
