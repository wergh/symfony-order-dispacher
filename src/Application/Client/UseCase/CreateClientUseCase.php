<?php

declare(strict_types=1);

namespace App\Application\Client\UseCase;

use App\Application\Client\DTO\ClientDTO;
use App\Domain\Client\Entity\Client;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

final class CreateClientUseCase
{
    public function __construct(private RepositoryFactoryInterface $repositoryFactory) {}

    public function execute(ClientDTO $dto): void
    {
        $clientRepository = $this->repositoryFactory->createClientRepository();

        // Transformar el DTO en una entidad de dominio
        $client = new Client($dto->name, $dto->surname);

        // Persistir la entidad
        $clientRepository->save($client);
    }
}
