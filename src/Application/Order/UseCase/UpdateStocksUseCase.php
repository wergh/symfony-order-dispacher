<?php

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;
use App\Domain\Shared\Interface\LoggerInterface;

final class UpdateStocksUseCase
{
    public function __construct(private RepositoryFactoryInterface $repositoryFactory, private LoggerInterface $logger) {}

    public function execute(Order $order): void
    {
        $this->logger->info('Estoy dentro del actualizador de stock');
        $productRepository = $this->repositoryFactory->createProductRepository();
        foreach ($order->getConcepts() as $concept) {
            $product = $productRepository->findById($concept->getProductId());
            if (null === $product) {
                throw new EntityNotFoundException('Product not found');
            }
            $product->decreaseStock($concept->getQuantity());
            $productRepository->save($product);
            $this->logger->info('Decrezco el stock para el producto '.$concept->getProductName());
        }
        $this->logger->info('Termino de repasar conceptos ');
    }
}
