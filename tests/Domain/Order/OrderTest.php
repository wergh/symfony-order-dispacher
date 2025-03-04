<?php

declare(strict_types=1);

namespace App\Tests\Domain\Order;

use App\Domain\Client\Entity\Client;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Enum\OrderStatusEnum;
use App\Domain\Order\ValueObject\OrderConcept;
use App\Domain\Product\Entity\Product;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testConstructor(): void
    {
        $order = new Order();

        $this->assertSame(OrderStatusEnum::PENDING, $order->getStatus());
        $this->assertFalse($order->isProcessed());
        $this->assertInstanceOf(\DateTime::class, $order->getCreatedAt());
    }


    public function testGetStatus(): void
    {
        $order = new Order();

        $this->assertSame(OrderStatusEnum::PENDING, $order->getStatus());
    }

    public function testSetStatus(): void
    {
        $order = new Order();
        $order->setStatus(OrderStatusEnum::REJECTED);

        $this->assertSame(OrderStatusEnum::REJECTED, $order->getStatus());
    }

    public function testGetCreatedAt(): void
    {
        $order = new Order();

        $this->assertInstanceOf(\DateTime::class, $order->getCreatedAt());
    }

    public function testIsProcessed(): void
    {
        $order = new Order();

        $this->assertFalse($order->isProcessed());
    }

    public function testSetProcessed(): void
    {
        $order = new Order();
        $order->markAsProcessed();

        $this->assertTrue($order->isProcessed());
    }

    public function testAddConcept(): void
    {
        $order = new Order();
        $concept = new OrderConcept($order, 1, 'Producto A', 10.99, 10, 1);
        $order->addConcept($concept);
        $this->assertTrue($order->getConcepts()->contains($concept));
    }
    public function testSetClient(): void
    {
        $order = new Order();
        $client = new Client('John', 'Doe');
        $order->setClient($client);
        $this->assertSame($client, $order->getClient());
    }
    public function testGetClient(): void
    {
        $order = new Order();
        $client = new Client('John', 'Doe');
        $order->setClient($client);

        $this->assertSame($client, $order->getClient());
    }
}
