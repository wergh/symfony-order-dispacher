<?php

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Product\Exception\InsufficientStockException;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

/**
 * Use case for updating the stocks of products in an order.
 */
final class UpdateStocksUseCase
{
    /**
     * UpdateStocksUseCase constructor.
     *
     * @param RepositoryFactoryInterface $repositoryFactory The factory to create repositories for domain entities.
     */
    public function __construct(private RepositoryFactoryInterface $repositoryFactory)
    {
    }

    /**
     * Executes the update of product stocks based on the order's concepts.
     *
     * @param Order $order The order containing the concepts to update stocks for.
     *
     * @return void
     *
     * @throws EntityNotFoundException If a product referenced in the order is not found.
     * @throws InsufficientStockException
     */
    public function execute(Order $order): void
    {
        $productRepository = $this->repositoryFactory->createProductRepository();
        foreach ($order->getConcepts() as $concept) {
            // Adds a random delay between 1 and 3 seconds for each concept
            sleep(rand(1, 3));
            $product = $productRepository->findById($concept->getProductId());
            if (null === $product) {
                throw new EntityNotFoundException('Product not found');
            }
            $product->decreaseStock($concept->getQuantity());
            $productRepository->save($product);
        }
    }
}
