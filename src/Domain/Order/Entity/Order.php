<?php

declare(strict_types=1);

namespace App\Domain\Order\Entity;

use App\Domain\Client\Entity\Client;
use App\Domain\Order\Enum\OrderStatusEnum;
use App\Domain\Order\Event\OrderStatusUpdatedEvent;
use App\Domain\Order\ValueObject\OrderConcept;
use App\Domain\Shared\Event\DomainEventInterface;
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

    private array $domainEvents = [];

    public function __construct()
    {
        $this->status = OrderStatusEnum::PENDING;
        $this->concepts = new ArrayCollection();
        $this->createdAt = Carbon::now();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): OrderStatusEnum
    {
        return $this->status;
    }

    public function setStatus(OrderStatusEnum $newStatus): void
    {
        $this->status = $newStatus;
    }

    public function isFinished(): bool
    {
        return OrderStatusEnum::APPROVED === $this->status || OrderStatusEnum::REJECTED === $this->status;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function addConcept(OrderConcept $concept): void
    {
        $this->concepts->add($concept);
    }

    public function totalAmount(): float
    {
        return array_sum($this->concepts->map(fn($concept) => $concept->total())->toArray());
    }

    public function markAsAccepted(): static
    {
        if ($this->status !== OrderStatusEnum::APPROVED) {
            $this->recordEvent(OrderStatusUpdatedEvent::fromOrder($this));
        }
        $this->status = OrderStatusEnum::APPROVED;

        return $this;
    }

    public function markAsRejected(): static
    {
        if ($this->status !== OrderStatusEnum::REJECTED) {
            $this->recordEvent(OrderStatusUpdatedEvent::fromOrder($this));
        }
        $this->status = OrderStatusEnum::REJECTED;

        return $this;
    }

    public function isAccepted(): bool
    {
        return ($this->status === OrderStatusEnum::APPROVED);
    }

    private function recordEvent(DomainEventInterface $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function pullEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
