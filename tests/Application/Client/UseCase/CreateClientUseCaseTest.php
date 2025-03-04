<?php

declare(strict_types=1);

namespace App\Tests\Application\Client\UseCase;

use App\Application\Client\DTO\ClientCreateDto;
use App\Application\Client\UseCase\CreateClientUseCase;
use App\Domain\Client\Entity\Client;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Tests\Infrastructure\Factory\MockRepositoryFactory;
use PHPUnit\Framework\TestCase;

class CreateClientUseCaseTest extends TestCase
{

    private MockRepositoryFactory $mockRepositoryFactory;
    private ClientRepositoryInterface $clientRepository;

    protected function setUp(): void
    {
        $this->mockRepositoryFactory = new MockRepositoryFactory();
        $this->clientRepository = $this->mockRepositoryFactory->createClientRepository();
    }



    public function testExecute(): void
    {

        $useCase = new CreateClientUseCase($this->mockRepositoryFactory);
        $dto = new ClientCreateDto('John', 'Doe');

        $this->mockRepositoryFactory->expectClientRepositorySave($this->callback(function($client) {
            $this->assertInstanceOf(Client::class, $client);
            $this->assertEquals('John', $client->getName());
            $this->assertEquals('Doe', $client->getSurname());
            return true;
        }));
        $useCase->execute($dto);

    }
}
