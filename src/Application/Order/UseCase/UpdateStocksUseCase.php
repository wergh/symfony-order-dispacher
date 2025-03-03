<?php

namespace App\Application\Order\UseCase;

use App\Domain\Order\Entity\Order;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;

final class UpdateStocksUseCase
{
    public function __construct(private RepositoryFactoryInterface $repositoryFactory, private LoggerInterface $logger)
    {
    }

    public function execute(Order $order): void
    {
        $productRepository = $this->repositoryFactory->createProductRepository();
        foreach ($order->getConcepts() as $concept) {
            //Añadimos un retardo aleatorio entre 1 y 3 segundos por cada concepto
            sleep(rand(1,3));
            $product = $productRepository->findById($concept->getProductId());
            if (null === $product) {
                throw new EntityNotFoundException('Product not found');
            }
            $product->decreaseStock($concept->getQuantity());
            $productRepository->save($product);
        }
    }
}
