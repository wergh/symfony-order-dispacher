<?php

namespace App\Tests\Infrastructure\Persistence\Doctrine\Product;

use App\Domain\Product\Entity\Product;
use App\Infrastructure\Persistence\Doctrine\Product\DoctrineProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class DoctrineProductRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $doctrineRepository;
    private DoctrineProductRepository $repository;

    protected function setUp(): void
    {
        $this->doctrineRepository = $this->createMock(EntityRepository::class);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getRepository')
            ->with(Product::class)
            ->willReturn($this->doctrineRepository);

        $this->repository = new DoctrineProductRepository($this->entityManager);
    }

    public function testFindById(): void
    {
        $product = new Product('Producto', 10.99, 10, 10);

        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($product);

        $result = $this->repository->findById(1);

        $this->assertSame($product, $result);
    }

    public function testAll(): void
    {
        $products = [
            new Product('Product A', 10.99, 10, 10),
            new Product('Product B', 9.99, 20, 20),
        ];

        $this->doctrineRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($products);

        $result = $this->repository->all();

        $this->assertCount(2, $result);
        $this->assertContains($products[0], $result->toArray());
        $this->assertContains($products[1], $result->toArray());
    }

    public function testSave(): void
    {
        $product = new Product('Producto', 10.99, 10, 10);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($product);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->repository->save($product);
    }
}
