<?php

declare(strict_types=1);

namespace App\Domain\Client\Factory;

use App\Domain\Client\Entity\Client;

/**
 * Factory class to create Client entities.
 */
class ClientFactory
{
    /**
     * Create a new Client instance.
     *
     * @param string $name The name of the client.
     * @param string $surname The surname of the client.
     *
     * @return Client The created Client entity.
     */
    public function create(string $name, string $surname): Client
    {
        return new Client($name, $surname);
    }
}
