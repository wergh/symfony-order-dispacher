<?php

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

final class UpdateStocksUseCase
{
    public function __construct(private RepositoryFactoryInterface $repositoryFactory) {}

    public function execute(Order $order): void
    {
        $productRepository = $this->repositoryFactory->createProductRepository();
        foreach ($order->getConcepts() as $concept) {
            $product = $productRepository->findById($concept->getProductId());
            if (null === $product) {
                throw new EntityNotFoundException('Product not found');
            }
            $product->decreaseStock($concept->getQuantity());
            $productRepository->save($product);
        }
    }
}
