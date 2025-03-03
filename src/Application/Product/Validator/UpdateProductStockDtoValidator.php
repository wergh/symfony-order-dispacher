<?php

declare(strict_types=1);

namespace App\Application\Product\Validator;

use App\Application\Product\DTO\UpdateProductStockDTO;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Validator service for validating the UpdateProductStockDTO.
 */
class UpdateProductStockDtoValidator
{
    /**
     * UpdateProductStockDtoValidator constructor.
     *
     * @param ValidatorInterface $validator The Symfony validator service.
     */
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * Validates the UpdateProductStockDTO and throws an exception if validation fails.
     *
     * @param UpdateProductStockDTO $dto The data transfer object to be validated.
     *
     * @throws ValidationFailedException If validation fails, throws an exception with the validation errors.
     *
     * @return void
     */
    public function validate(UpdateProductStockDTO $dto): void
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidationFailedException($dto, $errors);
        }
    }
}
