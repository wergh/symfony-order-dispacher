<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Client;

use App\Domain\Client\Entity\Client;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Doctrine implementation of the ClientRepositoryInterface.
 *
 * This class provides data access to Client entities using Doctrine ORM.
 */
class DoctrineClientRepository implements ClientRepositoryInterface
{
    /**
     * The Doctrine entity manager.
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager The Doctrine entity manager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retrieves all clients from the database.
     *
     * @return Collection A collection of Client entities
     */
    public function all(): Collection
    {
        $clients = $this->entityManager->getRepository(Client::class)->findAll();

        return new ArrayCollection($clients);
    }

    /**
     * Finds a client by its ID.
     *
     * @param int $id The client ID to find
     * @return Client|null The found Client entity or null if not found
     */
    public function findById(int $id): ?Client
    {
        return $this->entityManager->getRepository(Client::class)->find($id);
    }

    /**
     * Saves a client to the database.
     *
     * @param Client $client The client entity to save
     * @return void
     */
    public function save(Client $client): void
    {
        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }
}
