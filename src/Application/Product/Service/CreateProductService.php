<?php

declare(strict_types=1);

namespace App\Application\Product\Service;

use App\Application\Product\DTO\ProductDTO;
use App\Application\Product\UseCase\CreateProductUseCase;
use App\Application\Product\Validator\CreateProductDTOValidator;

class CreateProductService
{

    public function __construct(
        private CreateProductDTOValidator $dtoValidator,
        private CreateProductUseCase $createProductUseCase
    ) {}

    public function execute(ProductDTO $productDTO): void
    {
            $this->dtoValidator->validate($productDTO);

            $this->createProductUseCase->execute($productDTO);
    }
}
