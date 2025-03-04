<?php

namespace App\Tests\Infrastructure\Persistence\Doctrine\Order;


use App\Domain\Order\Entity\Order;
use App\Infrastructure\Persistence\Doctrine\Order\DoctrineOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class DoctrineOrderRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $doctrineRepository;
    private DoctrineOrderRepository $repository;

    protected function setUp(): void
    {
        $this->doctrineRepository = $this->createMock(EntityRepository::class);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getRepository')
            ->with(Order::class)
            ->willReturn($this->doctrineRepository);

        $this->repository = new DoctrineOrderRepository($this->entityManager);
    }

    public function testFindById(): void
    {
        $order = new Order();

        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($order);

        $result = $this->repository->findById(1);

        $this->assertSame($order, $result);
    }

    public function testSave(): void
    {
        $order = new Order();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($order);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->repository->save($order);
    }
}
