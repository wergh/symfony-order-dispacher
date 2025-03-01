<?php

declare(strict_types=1);

namespace App\Application\Product\UseCase;

use App\Application\Product\DTO\ProductDTO;
use App\Domain\Product\Entity\Product;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

final class CreateProductUseCase
{
    public function __construct(private RepositoryFactoryInterface $repositoryFactory) {}

    public function execute(ProductDTO $dto): void
    {
        $productRepository = $this->repositoryFactory->createProductRepository();

        // Transformar el DTO en una entidad de dominio
        $product = new Product($dto->name, $dto->price, $dto->tax, $dto->stock);

        // Persistir la entidad
        $productRepository->save($product);
    }
}
