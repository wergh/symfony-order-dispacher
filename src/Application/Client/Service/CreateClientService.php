<?php

declare(strict_types=1);

namespace App\Application\Client\Service;

use App\Application\Client\DTO\ClientDTO;
use App\Application\Client\UseCase\CreateClientUseCase;
use App\Application\Client\Validator\CreateClientDTOValidator;

class CreateClientService
{
    public function __construct(
        private CreateClientDTOValidator $dtoValidator,
        private CreateClientUseCase $createClientUseCase
    ) {}

    public function execute(ClientDTO $clientDTO): void
    {
        // Validar el DTO
        $this->dtoValidator->validate($clientDTO);

        // Llamar al caso de uso
        $this->createClientUseCase->execute($clientDTO);
    }
}
