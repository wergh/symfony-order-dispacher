<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Client;

use App\Domain\Client\Entity\Client;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineClientRepository implements ClientRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function all(): Collection
    {
        $clients = $this->entityManager->getRepository(Client::class)->findAll();

        return new ArrayCollection($clients);
    }

    public function findById(int $id): ?Client
    {
        return $this->entityManager->getRepository(Client::class)->find($id);
    }

    public function save(Client $client): void
    {
        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }
}
