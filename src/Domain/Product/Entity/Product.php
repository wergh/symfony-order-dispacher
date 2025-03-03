<?php

declare(strict_types=1);

namespace App\Domain\Product\Entity;

use App\Domain\Product\Exception\InsufficientStockException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private float $price;

    #[ORM\Column]
    private int $tax;

    #[ORM\Column]
    private int $stock;


    public function __construct(string $name, float $price, int $tax, int $stock)
    {
        $this->name= $name;
        $this->price = $price;
        $this->tax = $tax;
        $this->stock = $stock;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getTax(): int
    {
        return $this->tax;
    }

    public function setTax(int $tax): static
    {
        $this->tax = $tax;

        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function hasEnoughStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    public function decreaseStock(int $quantity): void
    {
        if (!$this->hasEnoughStock($quantity)) {
            throw new InsufficientStockException("Stock insuficiente para el producto.");
        }

        $this->stock -= $quantity;
    }
}
