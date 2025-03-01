<?php

declare(strict_types=1);

namespace App\Application\Product\Validator;

use App\Application\Product\DTO\UpdateProductStockDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class UpdateProductStockDTOValidator
{

    public function __construct(private ValidatorInterface $validator) {}

    public function validate(UpdateProductStockDTO $dto): void
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidationFailedException($dto, $errors);
        }
    }
}
