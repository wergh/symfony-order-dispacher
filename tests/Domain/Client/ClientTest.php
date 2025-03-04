<?php

declare(strict_types=1);

namespace App\Tests\Domain\Client;

use App\Domain\Client\Entity\Client;
use App\Domain\Order\Entity\Order;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{


    public function testGetName(): void
    {
        $client = new Client('John', 'Doe');

        $this->assertSame('John', $client->getName());
    }

    public function testSetName(): void
    {
        $client = new Client('John', 'Doe');
        $client->setName('Jane');

        $this->assertSame('Jane', $client->getName());
    }

    public function testGetSurname(): void
    {
        $client = new Client('John', 'Doe');

        $this->assertSame('Doe', $client->getSurname());
    }

    public function testSetSurname(): void
    {
        $client = new Client('John', 'Doe');
        $client->setSurname('Smith');

        $this->assertSame('Smith', $client->getSurname());
    }

    public function testAddOrder(): void
    {
        $client = new Client('John', 'Doe');
        $order = new Order();
        $client->addOrder($order);
        $this->assertTrue($client->getOrders()->contains($order));
    }
}
