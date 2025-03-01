<?php

declare(strict_types=1);

namespace App\Application\Client\Service;

use App\Domain\Client\Entity\Client;
use App\Domain\Client\Factory\ClientFactory;

class ClientFactoryService
{

    public function __construct(private ClientFactory $clientFactory) {}

    public function create(string $name, string $surname): Client
    {
        return $this->clientFactory->create($name, $surname);
    }
}
