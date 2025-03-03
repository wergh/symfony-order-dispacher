<?php

declare(strict_types=1);

namespace App\Application\Product\Service;

use App\Application\Product\DTO\UpdateProductStockDTO;
use App\Application\Product\UseCase\UpdateProductStockUseCase;
use App\Application\Product\Validator\UpdateProductStockDtoValidator;
use App\Domain\Shared\Exception\EntityNotFoundException;

/**
 * Service for updating the stock of a product.
 */
class UpdateProductStockService
{
    /**
     * UpdateProductStockService constructor.
     *
     * @param UpdateProductStockDtoValidator $dtoValidator          The DTO validator for the update product stock data.
     * @param UpdateProductStockUseCase      $updateProductStockUseCase The use case to update the product stock.
     */
    public function __construct(
        private UpdateProductStockDtoValidator $dtoValidator,
        private UpdateProductStockUseCase      $updateProductStockUseCase
    )
    {
    }

    /**
     * Executes the process of updating the product stock.
     *
     * @param UpdateProductStockDTO $updateProductStockDTO The data transfer object containing the product stock update details.
     *
     * @return void
     * @throws EntityNotFoundException
     */
    public function execute(UpdateProductStockDTO $updateProductStockDTO): void
    {
        $this->dtoValidator->validate($updateProductStockDTO);

        $this->updateProductStockUseCase->execute($updateProductStockDTO);
    }
}
