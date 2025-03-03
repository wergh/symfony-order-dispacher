<?php

declare(strict_types=1);

namespace App\Application\Product\Service;

use App\Application\Product\DTO\CreateProductDto;
use App\Application\Product\UseCase\CreateProductUseCase;
use App\Application\Product\Validator\CreateProductDtoValidator;

/**
 * Service for creating a product.
 */
class CreateProductService
{
    /**
     * CreateProductService constructor.
     *
     * @param CreateProductDtoValidator $dtoValidator The DTO validator for creating a product.
     * @param CreateProductUseCase      $createProductUseCase The use case for creating a product.
     */
    public function __construct(
        private CreateProductDtoValidator $dtoValidator,
        private CreateProductUseCase      $createProductUseCase
    )
    {
    }

    /**
     * Executes the process of creating a product.
     *
     * @param CreateProductDto $productDTO The data transfer object containing the product details.
     */
    public function execute(CreateProductDto $productDTO): void
    {
        $this->dtoValidator->validate($productDTO);

        $this->createProductUseCase->execute($productDTO);
    }
}
