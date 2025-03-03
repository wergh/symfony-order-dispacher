<?php

declare(strict_types=1);

namespace App\Application\Client\Service;

use App\Application\Client\DTO\ClientCreateDto;
use App\Application\Client\UseCase\CreateClientUseCase;
use App\Application\Client\Validator\CreateClientDtoValidator;

class CreateClientService
{
    public function __construct(
        private CreateClientDtoValidator $dtoValidator,
        private CreateClientUseCase      $createClientUseCase
    )
    {
    }

    public function execute(ClientCreateDto $clientDTO): void
    {
        // Validar el DTO
        $this->dtoValidator->validate($clientDTO);

        // Llamar al caso de uso
        $this->createClientUseCase->execute($clientDTO);
    }
}
