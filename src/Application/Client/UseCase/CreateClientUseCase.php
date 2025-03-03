<?php

declare(strict_types=1);

namespace App\Application\Client\UseCase;

use App\Application\Client\DTO\ClientCreateDto;
use App\Domain\Client\Entity\Client;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

/**
 * Use case for creating a client and saving it in the repository.
 */
final class CreateClientUseCase
{
    /**
     * CreateClientUseCase constructor.
     *
     * @param RepositoryFactoryInterface $repositoryFactory Factory for creating the client repository.
     */
    public function __construct(private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    /**
     * Executes the process of creating and saving a client.
     *
     * @param ClientCreateDto $dto The DTO containing the client data to be created.
     */
    public function execute(ClientCreateDto $dto): void
    {
        $clientRepository = $this->repositoryFactory->createClientRepository();

        $client = new Client($dto->name, $dto->surname);

        $clientRepository->save($client);
    }
}
