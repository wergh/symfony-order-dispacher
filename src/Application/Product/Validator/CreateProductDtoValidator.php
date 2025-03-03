<?php

declare(strict_types=1);

namespace App\Application\Product\Validator;

use App\Application\Product\DTO\CreateProductDto;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateProductDtoValidator
{

    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(CreateProductDto $dto): void
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidationFailedException($dto, $errors);
        }
    }
}
