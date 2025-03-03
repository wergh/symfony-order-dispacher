<?php

declare(strict_types=1);

namespace App\Application\Order\UseCase;

use App\Application\Order\DTO\CreateOrderDto;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\ValueObject\OrderConcept;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\Interface\RepositoryFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Use case for creating an order.
 */
final class CreateOrderUseCase
{
    /**
     * CreateOrderUseCase constructor.
     *
     * @param RepositoryFactoryInterface $repositoryFactory The factory to create repositories for domain entities.
     * @param ValidatorInterface $validator The validator for validating the input DTO.
     */
    public function __construct(
        private RepositoryFactoryInterface $repositoryFactory,
        private ValidatorInterface         $validator
    )
    {
    }

    /**
     * Executes the creation of an order.
     *
     * @param CreateOrderDto $createOrderDto The DTO containing the order data.
     *
     * @return Order The created order.
     *
     * @throws EntityNotFoundException if the client or any product in the order cannot be found.
     * @throws ValidationException if the DTO contains validation errors.
     */
    public function execute(CreateOrderDto $createOrderDto): Order
    {
        $clientRepository = $this->repositoryFactory->createClientRepository();
        $orderRepository = $this->repositoryFactory->createOrderRepository();
        $productRepository = $this->repositoryFactory->createProductRepository();

        $violations = $this->validator->validate($createOrderDto);
        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }

        $client = $clientRepository->findById($createOrderDto->clientId);
        if (null === $client) {
            throw new EntityNotFoundException('Client not found');
        }

        $order = new Order();
        $order->setClient($client);

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

        $orderRepository->save($order);

        return $order;
    }
}
