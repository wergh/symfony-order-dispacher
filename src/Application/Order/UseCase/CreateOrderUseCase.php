<?php

declare(strict_types=1);

namespace App\Application\Order\UseCase;

use App\Application\Order\DTO\OrderDTO;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\ValueObject\OrderConcept;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateOrderUseCase
{
    public function __construct(
        private RepositoryFactoryInterface $repositoryFactory,
        private ValidatorInterface $validator
    ) {
    }

    public function execute(OrderDTO $createOrderDto): Order
    {

        $clientRepository = $this->repositoryFactory->createClientRepository();
        $orderRepository = $this->repositoryFactory->createOrderRepository();
        $productRepository = $this->repositoryFactory->createProductRepository();

        // Validar el DTO usando el validador de Symfony
        $violations = $this->validator->validate($createOrderDto);
        if (count($violations) > 0) {
            // Manejar las violaciones de validación
            throw new ValidationException($violations);
        }

        // Obtener el cliente
        $client = $clientRepository->findById($createOrderDto->clientId);
        if (null === $client) {
            throw new EntityNotFoundException('Client not found');
        }

        // Crear la orden
        $order = new Order();
        $order->setClient($client);

        // Añadir los conceptos
        foreach ($createOrderDto->concepts as $conceptDto) {
            $product = $productRepository->findById($conceptDto->productId);
            if (null === $product) {
                throw new EntityNotFoundException('Product not found');
            }

            $orderConcept = new OrderConcept(
                $order,
                $product->getId(),
                $product->getName(),
                $product->getPrice(),
                $product->getTax(),
                $conceptDto->quantity
            );

            $order->addConcept($orderConcept);
        }

        // Persistir la orden
        $orderRepository->save($order);

        return $order;
    }
}
