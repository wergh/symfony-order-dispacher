<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Order;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Doctrine implementation of the OrderRepositoryInterface.
 *
 * This class provides data access to Order entities using Doctrine ORM.
 */
class DoctrineOrderRepository implements OrderRepositoryInterface
{
    /**
     * The Doctrine entity manager.
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager The Doctrine entity manager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Finds an order by its ID.
     *
     * @param int $id The order ID to find
     * @return Order|null The found Order entity or null if not found
     */
    public function findById(int $id): ?Order
    {
        return $this->entityManager->getRepository(Order::class)->find($id);
    }

    /**
     * Saves an order to the database.
     *
     * @param Order $order The order entity to save
     * @return void
     */
    public function save(Order $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}
