<?php

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Product\Exception\InsufficientStockException;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

/**
 * Use case for validating the stock of products in an order.
 */
final class ValidateStockUseCase
{
    /**
     * ValidateStockUseCase constructor.
     *
     * @param RepositoryFactoryInterface $repositoryFactory The factory to create repositories for domain entities.
     */
    public function __construct(private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    /**
     * Validates that all products in the order have sufficient stock.
     *
     * @param Order $order The order containing the concepts to validate the stock for.
     *
     * @return void
     *
     * @throws EntityNotFoundException If a product referenced in the order is not found.
     * @throws InsufficientStockException If there is not enough stock for any product in the order.
     */
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
