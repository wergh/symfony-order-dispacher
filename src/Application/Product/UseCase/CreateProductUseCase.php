<?php

declare(strict_types=1);

namespace App\Application\Product\UseCase;

use App\Application\Product\DTO\CreateProductDto;
use App\Domain\Product\Entity\Product;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

final class CreateProductUseCase
{

    public function __construct(private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    public function execute(CreateProductDto $dto): void
    {
        $productRepository = $this->repositoryFactory->createProductRepository();

        $product = new Product($dto->name, $dto->price, $dto->tax, $dto->stock);

        $productRepository->save($product);
    }
}
