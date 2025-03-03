<?php

declare(strict_types=1);

namespace App\Domain\Client\Repository;

use App\Domain\Client\Entity\Client;
use Doctrine\Common\Collections\Collection;

interface ClientRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Client;

    public function save(Client $client): void;
}
