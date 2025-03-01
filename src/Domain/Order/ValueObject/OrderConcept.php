<?php

declare(strict_types=1);

namespace App\Domain\Order\ValueObject;

use App\Domain\Order\Entity\Order;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_concepts')]
#[ORM\UniqueConstraint(name: "order_product_unique", columns: ["order_id", "product_id"])]
final class OrderConcept
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'concepts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $productId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $productName;

    #[ORM\Column(type: 'float')]
    private float $unitPrice;

    #[ORM\Column(type: 'integer')]
    private int $tax;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    public function __construct(Order $order, int $productId, string $productName, float $unitPrice, int $tax, int $quantity)
    {
        $this->order = $order;
        $this->productId = $productId;
        $this->productName = $productName;
        $this->unitPrice = $unitPrice;
        $this->tax = $tax;
        $this->quantity = $quantity;
    }

    public function getTotal(): float
    {
        return $this->unitPrice * (1 + ($this->tax / 100)) * $this->quantity;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function getTax(): int
    {
        return $this->tax;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
