<?php

declare(strict_types=1);

namespace App\Application\Product\Service;

use App\Application\Product\DTO\UpdateProductStockDTO;
use App\Application\Product\UseCase\UpdateProductStockUseCase;
use App\Application\Product\Validator\UpdateProductStockDtoValidator;

class UpdateProductStockService
{

    public function __construct(
        private UpdateProductStockDtoValidator $dtoValidator,
        private UpdateProductStockUseCase      $updateProductStockUseCase
    )
    {
    }

    public function execute(UpdateProductStockDTO $updateProductStockDTO): void
    {
        $this->dtoValidator->validate($updateProductStockDTO);

        $this->updateProductStockUseCase->execute($updateProductStockDTO);
    }
}
