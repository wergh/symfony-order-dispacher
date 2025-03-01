<?php

declare(strict_types=1);

namespace App\Domain\Client\Factory;

use App\Domain\Client\Entity\Client;

class ClientFactory
{

    public function create(string $name, string $surname): Client
    {
        return new Client($name, $surname);
    }
}
