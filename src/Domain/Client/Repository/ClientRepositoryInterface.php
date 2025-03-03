<?php

declare(strict_types=1);

namespace App\Domain\Client\Repository;

use App\Domain\Client\Entity\Client;
use Doctrine\Common\Collections\Collection;

/**
 * Interface for client repository.
 */
interface ClientRepositoryInterface
{
    /**
     * Get all clients.
     *
     * @return Collection A collection of Client entities.
     */
    public function all(): Collection;

    /**
     * Find a client by its ID.
     *
     * @param int $id The ID of the client.
     *
     * @return Client|null The client entity or null if not found.
     */
    public function findById(int $id): ?Client;

    /**
     * Save a client entity.
     *
     * @param Client $client The client entity to save.
     */
    public function save(Client $client): void;
}
