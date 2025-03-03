<?php

declare(strict_types=1);

namespace App\Application\Client\Service;

use App\Domain\Client\Entity\Client;
use App\Domain\Client\Factory\ClientFactory;

/**
 * Service responsible for creating a Client entity using the ClientFactory.
 */
class ClientFactoryService
{

    /**
     * ClientFactoryService constructor.
     *
     * @param ClientFactory $clientFactory The factory used to create clients.
     */
    public function __construct(private ClientFactory $clientFactory)
    {
    }

    /**
     * Creates a new Client entity.
     *
     * @param string $name    The client's first name.
     * @param string $surname The client's last name.
     *
     * @return Client The created Client entity.
     */
    public function create(string $name, string $surname): Client
    {
        return $this->clientFactory->create($name, $surname);
    }
}
