<?php

declare(strict_types=1);

namespace App\Application\Product\Validator;

use App\Application\Product\DTO\CreateProductDto;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Validator service for validating the CreateProductDto.
 */
class CreateProductDtoValidator
{
    /**
     * CreateProductDtoValidator constructor.
     *
     * @param ValidatorInterface $validator The Symfony validator service.
     */
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * Validates the CreateProductDto and throws an exception if validation fails.
     *
     * @param CreateProductDto $dto The data transfer object to be validated.
     *
     * @throws ValidationFailedException If validation fails, throws an exception with the validation errors.
     *
     * @return void
     */
    public function validate(CreateProductDto $dto): void
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidationFailedException($dto, $errors);
        }
    }
}
