<?php

namespace App\Tests\Infrastructure\Persistence\Doctrine\EventListener;

use App\Domain\Client\Entity\Client;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatusEnum;
use App\Domain\Order\Event\OrderCreatedEvent;
use App\Domain\Order\Event\OrderStatusUpdatedEvent;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\MonitoringInterface;
use App\Infrastructure\Persistence\Doctrine\EventListener\OrderEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderEntityListenerTest extends TestCase
{
    private MessageBusInterface $bus;
    private LoggerInterface $logger;
    private MonitoringInterface $monitoring;
    private OrderEntityListener $listener;

    protected function setUp(): void
    {
        \DG\BypassFinals::enable();
        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->monitoring = $this->createMock(MonitoringInterface::class);

        $this->listener = new OrderEntityListener(
            $this->bus,
            $this->logger,
            $this->monitoring
        );
    }

    public function testPostPersist(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(2);
        $order = $this->createMock(Order::class);
        $order->method('getId')->willReturn(1);
        $order->method('getClient')->willReturn($client);

        $eventArgs = $this->createMock(PostPersistEventArgs::class);

        $this->bus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function($event) {
                return $event instanceof OrderCreatedEvent
                    && $event->getOrderId() === 1;
            }))
            ->willReturn($this->createMock(Envelope::class));

        $this->listener->postPersist($order, $eventArgs);
    }

    public function testPostPersistWithException(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(2);
        $order = $this->createMock(Order::class);
        $order->method('getId')->willReturn(1);
        $order->method('getClient')->willReturn($client);

        $eventArgs = $this->createMock(PostPersistEventArgs::class);
        $exception = $this->createMock(ExceptionInterface::class);

        $this->bus->expects($this->once())
            ->method('dispatch')
            ->willThrowException($exception);

        $this->monitoring->expects($this->once())
            ->method('captureException')
            ->with($exception);

        $this->listener->postPersist($order, $eventArgs);
    }

    public function testPreUpdateAndPostUpdate(): void
    {
        $order = $this->createMock(Order::class);
        $order->method('getId')->willReturn(1);
        $order->method('getStatus')->willReturn(OrderStatusEnum::APPROVED);

        $preUpdateEventArgs = $this->createMock(PreUpdateEventArgs::class);
        $preUpdateEventArgs->method('hasChangedField')->with('status')->willReturn(true);
        $preUpdateEventArgs->method('getOldValue')->with('status')->willReturn(OrderStatusEnum::PENDING->value);

        $postUpdateEventArgs = $this->createMock(PostUpdateEventArgs::class);

        $this->bus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function($event) {
                return $event instanceof OrderStatusUpdatedEvent
                    && $event->getOrderId() === 1;
            }))
            ->willReturn($this->createMock(Envelope::class));

        $this->listener->preUpdate($order, $preUpdateEventArgs);
        $this->listener->postUpdate($order, $postUpdateEventArgs);
    }
}
