<?php

namespace App\Tests\Infrastructure\Persistence\Doctrine\Client;

use App\Domain\Client\Entity\Client;
use App\Infrastructure\Persistence\Doctrine\Client\DoctrineClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class DoctrineClientRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $doctrineRepository;
    private DoctrineClientRepository $repository;

    protected function setUp(): void
    {
        $this->doctrineRepository = $this->createMock(EntityRepository::class);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getRepository')
            ->with(Client::class)
            ->willReturn($this->doctrineRepository);

        $this->repository = new DoctrineClientRepository($this->entityManager);
    }

    public function testFindById(): void
    {
        $client = new Client('John', 'Doe');

        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($client);

        $result = $this->repository->findById(1);

        $this->assertSame($client, $result);
    }

    public function testAll(): void
    {
        $clients = [
            new Client('John', 'Doe'),
            new Client('Jane', 'Smith')
        ];

        $this->doctrineRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($clients);

        $result = $this->repository->all();

        $this->assertCount(2, $result);
        $this->assertContains($clients[0], $result->toArray());
        $this->assertContains($clients[1], $result->toArray());
    }

    public function testSave(): void
    {
        $client = new Client('John', 'Doe');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($client);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->repository->save($client);
    }
}
