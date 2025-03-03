<?php

declare(strict_types=1);

namespace App\Application\Client\Service;

use App\Application\Client\DTO\ClientCreateDto;
use App\Application\Client\UseCase\CreateClientUseCase;
use App\Application\Client\Validator\CreateClientDtoValidator;

/**
 * Service responsible for creating a client by validating the DTO and calling the use case.
 */
class CreateClientService
{
    /**
     * CreateClientService constructor.
     *
     * @param CreateClientDtoValidator $dtoValidator Validator to validate the ClientCreateDto.
     * @param CreateClientUseCase      $createClientUseCase Use case to create a client.
     */
    public function __construct(
        private CreateClientDtoValidator $dtoValidator,
        private CreateClientUseCase      $createClientUseCase
    )
    {
    }

    /**
     * Executes the process of validating the client DTO and creating the client.
     *
     * @param ClientCreateDto $clientDTO The DTO containing the client data to be created.
     */
    public function execute(ClientCreateDto $clientDTO): void
    {
        // Validar el DTO
        $this->dtoValidator->validate($clientDTO);

        // Llamar al caso de uso
        $this->createClientUseCase->execute($clientDTO);
    }
}
