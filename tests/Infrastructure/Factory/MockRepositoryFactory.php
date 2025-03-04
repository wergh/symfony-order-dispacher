<?php

namespace App\Tests\Infrastructure\Factory;

use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;
use PHPUnit\Framework\TestCase;

class MockRepositoryFactory extends TestCase implements RepositoryFactoryInterface
{
    private ?ClientRepositoryInterface $clientRepository = null;
    private ?OrderRepositoryInterface $orderRepository = null;
    private ?ProductRepositoryInterface $productRepository = null;

    public function createClientRepository(): ClientRepositoryInterface
    {
        if ($this->clientRepository === null) {
            $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        }
        return $this->clientRepository;
    }

    public function createOrderRepository(): OrderRepositoryInterface
    {
        if ($this->orderRepository === null) {
            $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        }
        return $this->orderRepository;
    }

    public function createProductRepository(): ProductRepositoryInterface
    {
        if ($this->productRepository === null) {
            $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        }
        return $this->productRepository;
    }

    public function expectOrderRepositoryFindById(int $id, mixed $returnValue): void
    {
        $this->orderRepository->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($returnValue);
    }

    public function expectOrderRepositorySaveMarkAsAccepted(Order $order): void
    {
        $this->createOrderRepository()->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($savedOrder) use ($order) {
                return $savedOrder === $order && $savedOrder->isAccepted();
            }));
    }

    public function expectOrderRepositorySaveMarkAsFailed(Order $order): void
    {
        $this->createOrderRepository()->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($savedOrder) use ($order) {
                return $savedOrder === $order && !($savedOrder->isAccepted());
            }));
    }

    public function expectOrderRepositorySaveMarkAsRejected(Order $order): void
    {
        $this->createOrderRepository()->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($savedOrder) use ($order) {
                return $savedOrder === $order && !($savedOrder->isAccepted());
            }));
    }
    public function expectClientRepositoryFindById($id, $returnValue): void
    {
        $this->createClientRepository()->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($returnValue);
    }

    public function expectProductRepositoryFindById($id, $returnValue): void
    {
        $this->createProductRepository()->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($returnValue);
    }

    public function expectOrderRepositorySave($order): void
    {
        $this->createOrderRepository()->expects($this->once())
            ->method('save')
            ->with($order);
    }

    public function expectProductRepositorySave($product): void
    {
        $this->createProductRepository()->expects($this->once())
            ->method('save')
            ->with($product);
    }

    public function expectClientRepositorySave($client): void
    {
        $this->createClientRepository()->expects($this->once())
            ->method('save')
            ->with($client);
    }

}
