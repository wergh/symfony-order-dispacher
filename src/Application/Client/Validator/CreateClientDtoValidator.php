<?php

declare(strict_types=1);

namespace App\Application\Client\Validator;

use App\Application\Client\DTO\ClientCreateDto;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class CreateClientDtoValidator
{

    public function __construct(private ValidatorInterface $validator) {}

    public function validate(ClientCreateDto $dto): void
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidationFailedException($dto, $errors);
        }
    }
}
