<?php

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Product\Exception\InsufficientStockException;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

final class ValidateStockUseCase
{

    public function __construct(private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    public function execute(Order $order): void
    {
        $productRepository = $this->repositoryFactory->createProductRepository();
        foreach ($order->getConcepts() as $concept) {
            $product = $productRepository->findById($concept->getProductId());
            if (null === $product) {
                throw new EntityNotFoundException('Product not found');
            }
            if (!$product->hasEnoughStock($concept->getQuantity())) {
                throw new InsufficientStockException('Insufficient stock for product: ' . $product->getName());
            }
        }
    }

}
