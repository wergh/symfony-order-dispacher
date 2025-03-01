<?php

declare(strict_types=1);

namespace App\Application\Product\UseCase;

use App\Application\Product\DTO\UpdateProductStockDTO;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

final class UpdateProductStockUseCase
{
    public function __construct(private RepositoryFactoryInterface $repositoryFactory) {}

    public function execute(UpdateProductStockDTO $dto): void
    {
        $productRepository = $this->repositoryFactory->createProductRepository();

        $product = $productRepository->findById($dto->id);
        if (!$product) {
            throw new EntityNotFoundException('Product not found');
        }


        $product->setStock($dto->stock);

        $productRepository->save($product);

    }
}
