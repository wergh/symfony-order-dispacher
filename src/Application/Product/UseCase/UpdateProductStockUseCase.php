<?php

declare(strict_types=1);

namespace App\Application\Product\UseCase;

use App\Application\Product\DTO\UpdateProductStockDTO;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

/**
 * Use case for updating the stock of a product.
 */
final class UpdateProductStockUseCase
{
    /**
     * UpdateProductStockUseCase constructor.
     *
     * @param RepositoryFactoryInterface $repositoryFactory The factory to create the repositories.
     */
    public function __construct(private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    /**
     * Executes the process of updating the stock of a product.
     *
     * @param UpdateProductStockDTO $dto The data transfer object containing the product ID and the new stock value.
     *
     * @throws EntityNotFoundException If the product is not found in the repository.
     *
     * @return void
     */
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
