<?php

declare(strict_types=1);

namespace App\Application\Client\Validator;

use App\Application\Client\DTO\ClientCreateDto;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Validator service for validating the ClientCreateDto.
 */
class CreateClientDtoValidator
{
    /**
     * CreateClientDtoValidator constructor.
     *
     * @param ValidatorInterface $validator Symfony validator instance to validate the DTO.
     */
    public function __construct(private ValidatorInterface $validator) {}

    /**
     * Validates the given ClientCreateDto.
     *
     * @param ClientCreateDto $dto The DTO containing the client data to be validated.
     *
     * @throws ValidationFailedException If validation fails.
     */
    public function validate(ClientCreateDto $dto): void
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidationFailedException($dto, $errors);
        }
    }
}
