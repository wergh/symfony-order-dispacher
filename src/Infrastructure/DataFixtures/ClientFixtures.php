<?php

declare(strict_types=1);

namespace App\Infrastructure\DataFixtures;

use App\Application\Client\Service\ClientFactoryService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
class ClientFixtures extends Fixture
{

    public function __construct(private ClientFactoryService $clientFactoryService)   {}

    public function load(ObjectManager $manager): void
    {
        $client = $this->clientFactoryService->create("John", "Doe");
        $manager->persist($client);
        $manager->flush();
        $client = $this->clientFactoryService->create("Jane", "Smith");
        $manager->persist($client);
        $manager->flush();
        $client = $this->clientFactoryService->create("Bob", "Johnson");
        $manager->persist($client);
        $manager->flush();
    }
}
