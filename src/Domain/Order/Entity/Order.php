<?php

declare(strict_types=1);

namespace App\Domain\Order\Entity;

use App\Domain\Client\Entity\Client;
use App\Domain\Order\Enum\OrderStatusEnum;
use App\Domain\Order\ValueObject\OrderConcept;
use Carbon\Carbon;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(enumType: OrderStatusEnum::class)]
    private OrderStatusEnum $status;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'orders')]
    private Client $client;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderConcept::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $concepts;

    #[ORM\Column]
    private DateTime $createdAt;

    #[ORM\Column]
    private bool $processed;

    /**
     * Order constructor.
     * Initializes the order with a pending status, an empty concepts collection, the current date and time, and not processed.
     */
    public function __construct()
    {
        $this->status = OrderStatusEnum::PENDING;
        $this->concepts = new ArrayCollection();
        $this->createdAt = Carbon::now();
        $this->processed = false;
    }

    /**
     * Get the ID of the order.
     *
     * @return int|null The ID of the order.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the status of the order.
     *
     * @return OrderStatusEnum The status of the order.
     */
    public function getStatus(): OrderStatusEnum
    {
        return $this->status;
    }

    /**
     * Set a new status for the order.
     *
     * @param OrderStatusEnum $newStatus The new status to set.
     */
    public function setStatus(OrderStatusEnum $newStatus): void
    {
        $this->status = $newStatus;
    }

    /**
     * Check if the order is finished.
     *
     * @return bool True if the order is finished (i.e. its status is not PENDING), false otherwise.
     */
    public function isFinished(): bool
    {
        return OrderStatusEnum::PENDING != $this->status;
    }

    /**
     * Get the client associated with the order.
     *
     * @return Client The client associated with the order.
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Set the client for the order.
     *
     * @param Client $client The client to associate with the order.
     * @return static The current order instance.
     */
    public function setClient(Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get the creation date and time of the order.
     *
     * @return Carbon The creation date and time of the order.
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * Add a concept (item) to the order.
     *
     * @param OrderConcept $concept The concept (item) to add to the order.
     */
    public function addConcept(OrderConcept $concept): void
    {
        $this->concepts->add($concept);
    }

    /**
     * Get the collection of concepts (items) in the order.
     *
     * @return Collection The collection of concepts (items) in the order.
     */
    public function getConcepts(): Collection
    {
        return $this->concepts;
    }

    /**
     * Get the total amount of the order (sum of all concepts).
     *
     * @return float The total amount of the order.
     */
    public function totalAmount(): float
    {
        return array_sum($this->concepts->map(fn($concept) => $concept->total())->toArray());
    }

    /**
     * Mark the order as accepted (approved).
     *
     * @return static The current order instance.
     */
    public function markAsAccepted(): static
    {
        $this->status = OrderStatusEnum::APPROVED;
        return $this;
    }

    /**
     * Mark the order as rejected.
     *
     * @return static The current order instance.
     */
    public function markAsRejected(): static
    {
        $this->status = OrderStatusEnum::REJECTED;
        return $this;
    }

    /**
     * Mark the order as failed.
     *
     * @return static The current order instance.
     */
    public function markAsFailed(): static
    {
        $this->status = OrderStatusEnum::FAILED;
        return $this;
    }

    /**
     * Check if the order is accepted (approved).
     *
     * @return bool True if the order is accepted, false otherwise.
     */
    public function isAccepted(): bool
    {
        return ($this->status === OrderStatusEnum::APPROVED);
    }

    /**
     * Check if the order has been processed.
     *
     * @return bool True if the order has been processed, false otherwise.
     */
    public function isProcessed(): bool
    {
        return $this->processed;
    }

    /**
     * Mark the order as processed.
     *
     * @return static The current order instance.
     */
    public function markAsProcessed(): static
    {
        $this->processed = true;
        return $this;
    }

}
