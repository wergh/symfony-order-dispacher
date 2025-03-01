<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Domain\Shared\Event\DomainEventInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

final class RabbitMqEventBus implements EventBusInterface
{
    private MessageBusInterface $bus;
    private TransportInterface $transport;
    public function __construct(MessageBusInterface $bus, TransportInterface $transport)
    {
        $this->bus = $bus;
        $this->transport = $transport;
    }

    public function dispatch(DomainEventInterface $event): void
    {
        $envelope = new Envelope($event);
        $this->bus->dispatch($envelope);
        $this->transport->send($envelope);
    }
}
