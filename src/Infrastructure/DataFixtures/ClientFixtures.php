<?php

declare(strict_types=1);

namespace App\Infrastructure\DataFixtures;

use App\Application\Client\Service\ClientFactoryService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Fixture for loading client data.
 */
class ClientFixtures extends Fixture
{

    /**
     * ClientFixtures constructor.
     *
     * @param ClientFactoryService $clientFactoryService
     */
    public function __construct(private ClientFactoryService $clientFactoryService)
    {
    }

    /**
     * Load sample clients into the database.
     *
     * @param ObjectManager $manager
     */
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
