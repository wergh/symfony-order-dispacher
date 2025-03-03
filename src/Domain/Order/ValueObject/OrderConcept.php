<?php

declare(strict_types=1);

namespace App\Domain\Order\ValueObject;

use App\Domain\Order\Entity\Order;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a concept (line item) in an order.
 *
 * An order concept represents a product within an order, including details like product ID,
 * product name, unit price, tax rate, and quantity. It also calculates the total price
 * based on these values.
 */
#[ORM\Entity]
#[ORM\Table(name: 'order_concepts')]
#[ORM\UniqueConstraint(name: "order_product_unique", columns: ["order_id", "product_id"])]
final readonly class OrderConcept
{
    /**
     * @var Order The order to which this concept belongs.
     */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'concepts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Order $order;

    /**
     * @var int The product ID for the concept.
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $productId;

    /**
     * @var string The name of the product for this concept.
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $productName;

    /**
     * @var float The unit price of the product for this concept.
     */
    #[ORM\Column(type: 'float')]
    private float $unitPrice;

    /**
     * @var int The tax rate (percentage) applied to the product.
     */
    #[ORM\Column(type: 'integer')]
    private int $tax;

    /**
     * @var int The quantity of the product in this concept.
     */
    #[ORM\Column(type: 'integer')]
    private int $quantity;

    /**
     * Constructor for OrderConcept.
     *
     * @param Order $order The order to which this concept belongs.
     * @param int $productId The product ID for this concept.
     * @param string $productName The name of the product.
     * @param float $unitPrice The unit price of the product.
     * @param int $tax The tax rate for the product.
     * @param int $quantity The quantity of the product.
     */
    public function __construct(Order $order, int $productId, string $productName, float $unitPrice, int $tax, int $quantity)
    {
        $this->order = $order;
        $this->productId = $productId;
        $this->productName = $productName;
        $this->unitPrice = $unitPrice;
        $this->tax = $tax;
        $this->quantity = $quantity;
    }

    /**
     * Get the total amount for this concept, including tax and quantity.
     *
     * @return float The total price for this concept.
     */
    public function getTotal(): float
    {
        return $this->unitPrice * (1 + ($this->tax / 100)) * $this->quantity;
    }

    /**
     * Get the order to which this concept belongs.
     *
     * @return Order The order entity.
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * Get the product ID for this concept.
     *
     * @return int The product ID.
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * Get the product name for this concept.
     *
     * @return string The product name.
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * Get the unit price for this concept.
     *
     * @return float The unit price of the product.
     */
    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    /**
     * Get the tax rate applied to the product.
     *
     * @return int The tax rate (percentage).
     */
    public function getTax(): int
    {
        return $this->tax;
    }

    /**
     * Get the quantity of the product for this concept.
     *
     * @return int The quantity.
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
