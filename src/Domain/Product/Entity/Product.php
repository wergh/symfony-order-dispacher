<?php

declare(strict_types=1);

namespace App\Domain\Product\Entity;

use App\Domain\Product\Exception\InsufficientStockException;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a product in the system.
 *
 * A product includes details such as its name, price, tax rate, and available stock.
 * It also contains methods to manage and update stock levels.
 */
#[ORM\Entity]
class Product
{
    /**
     * @var int The unique identifier of the product.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    /**
     * @var string The name of the product.
     */
    #[ORM\Column(length: 255)]
    private string $name;

    /**
     * @var float The price of the product.
     */
    #[ORM\Column]
    private float $price;

    /**
     * @var int The tax rate for the product (in percentage).
     */
    #[ORM\Column]
    private int $tax;

    /**
     * @var int The available stock of the product.
     */
    #[ORM\Column]
    private int $stock;


    /**
     * Product constructor.
     *
     * @param string $name The name of the product.
     * @param float $price The price of the product.
     * @param int $tax The tax rate for the product.
     * @param int $stock The available stock of the product.
     */
    public function __construct(string $name, float $price, int $tax, int $stock)
    {
        $this->name = $name;
        $this->price = $price;
        $this->tax = $tax;
        $this->stock = $stock;
    }

    /**
     * Get the product's unique identifier.
     *
     * @return int The product ID.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the name of the product.
     *
     * @return string The product name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name of the product.
     *
     * @param string $name The new name of the product.
     * @return static The current instance for method chaining.
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the price of the product.
     *
     * @return float The product price.
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Set the price of the product.
     *
     * @param float $price The new price of the product.
     * @return static The current instance for method chaining.
     */
    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the tax rate for the product.
     *
     * @return int The tax rate (in percentage).
     */
    public function getTax(): int
    {
        return $this->tax;
    }

    /**
     * Set the tax rate for the product.
     *
     * @param int $tax The new tax rate (in percentage).
     * @return static The current instance for method chaining.
     */
    public function setTax(int $tax): static
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get the available stock for the product.
     *
     * @return int The available stock.
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * Set the stock quantity for the product.
     *
     * @param int $stock The new stock quantity.
     * @return static The current instance for method chaining.
     */
    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Decrease the stock of the product by the specified quantity.
     *
     * Throws an exception if there is not enough stock available.
     *
     * @param int $quantity The quantity to decrease.
     * @throws InsufficientStockException If there is not enough stock.
     */
    public function decreaseStock(int $quantity): void
    {
        if (!$this->hasEnoughStock($quantity)) {
            throw new InsufficientStockException("Stock insuficiente para el producto.");
        }

        $this->stock -= $quantity;
    }

    /**
     * Check if the product has enough stock for the specified quantity.
     *
     * @param int $quantity The quantity to check.
     * @return bool True if there is enough stock, false otherwise.
     */
    public function hasEnoughStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }
}
