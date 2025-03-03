<?php

declare(strict_types=1);

namespace App\Application\Product\UseCase;

use App\Application\Product\DTO\CreateProductDto;
use App\Domain\Product\Entity\Product;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

/**
 * Use case for creating a new product.
 */
final class CreateProductUseCase
{
    /**
     * CreateProductUseCase constructor.
     *
     * @param RepositoryFactoryInterface $repositoryFactory The factory to create the repositories.
     */
    public function __construct(private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    /**
     * Executes the process of creating a new product.
     *
     * @param CreateProductDto $dto The data transfer object containing the product details.
     *
     * @return void
     */
    public function execute(CreateProductDto $dto): void
    {
        $productRepository = $this->repositoryFactory->createProductRepository();

        $product = new Product($dto->name, $dto->price, $dto->tax, $dto->stock);

        $productRepository->save($product);
    }
}
