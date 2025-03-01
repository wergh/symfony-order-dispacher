<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EventListener;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Event\OrderCreatedEvent;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderCreatedListener
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $order = $args->getObject();

        if ($order instanceof Order) {
            $event = OrderCreatedEvent::fromOrder($order);
            $this->bus->dispatch($event);
        }
    }
}
