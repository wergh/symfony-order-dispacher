<?php

declare(strict_types=1);

namespace App\Application\Product\Service;

use App\Application\Product\DTO\CreateProductDto;
use App\Application\Product\UseCase\CreateProductUseCase;
use App\Application\Product\Validator\CreateProductDtoValidator;

class CreateProductService
{

    public function __construct(
        private CreateProductDtoValidator $dtoValidator,
        private CreateProductUseCase      $createProductUseCase
    )
    {
    }

    public function execute(CreateProductDto $productDTO): void
    {
        $this->dtoValidator->validate($productDTO);

        $this->createProductUseCase->execute($productDTO);
    }
}
