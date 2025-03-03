<?php

declare(strict_types=1);

namespace App\Application\Client\UseCase;

use App\Application\Client\DTO\ClientCreateDto;
use App\Domain\Client\Entity\Client;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

final class CreateClientUseCase
{

    public function __construct(private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    public function execute(ClientCreateDto $dto): void
    {
        $clientRepository = $this->repositoryFactory->createClientRepository();

        $client = new Client($dto->name, $dto->surname);

        $clientRepository->save($client);
    }
}
