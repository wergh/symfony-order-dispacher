<?php

declare(strict_types=1);

namespace App\Tests\Domain\Product;

use App\Domain\Product\Entity\Product;
use App\Domain\Product\Exception\InsufficientStockException;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testConstructor(): void
    {
        $product = new Product('Product A', 10.99, 21, 100);

        $this->assertSame('Product A', $product->getName());
        $this->assertSame(10.99, $product->getPrice());
        $this->assertSame(21, $product->getTax());
        $this->assertSame(100, $product->getStock());
    }


    public function testGetName(): void
    {
        $product = new Product('Product A', 10.99, 21, 100);

        $this->assertSame('Product A', $product->getName());
    }

    public function testSetName(): void
    {
        $product = new Product('Product A', 10.99, 21, 100);
        $product->setName('Product B');

        $this->assertSame('Product B', $product->getName());
    }

    public function testGetPrice(): void
    {
        $product = new Product('Product A', 10.99, 21, 100);

        $this->assertSame(10.99, $product->getPrice());
    }

    public function testSetPrice(): void
    {
        $product = new Product('Product A', 10.99, 21, 100);
        $product->setPrice(19.99);

        $this->assertSame(19.99, $product->getPrice());
    }

    public function testGetTax(): void
    {
        $product = new Product('Product A', 10.99, 21, 100);

        $this->assertSame(21, $product->getTax());
    }

    public function testSetTax(): void
    {
        $product = new Product('Product A', 10.99, 21, 100);
        $product->setTax(10);

        $this->assertSame(10, $product->getTax());
    }

    public function testGetStock(): void
    {
        $product = new Product('Product A', 10.99, 21, 100);

        $this->assertSame(100, $product->getStock());
    }

    public function testSetStock(): void
    {
        $product = new Product('Product A', 10.99, 21, 100);
        $product->setStock(50);

        $this->assertSame(50, $product->getStock());
    }

    public function testDecreaseStock(): void
    {
        $product = new Product('Product A', 10.99, 21, 100);
        $product->decreaseStock(20);

        $this->assertSame(80, $product->getStock());
    }

    public function testDecreaseStockInsufficient(): void
    {
        $product = new Product('Product A', 10.99, 21, 100);
        $this->expectException(InsufficientStockException::class);
        $product->decreaseStock(150);
    }

    public function testHasEnoughStock(): void
    {
        $product = new Product('Product A', 10.99, 21, 100);

        $this->assertTrue($product->hasEnoughStock(50));
        $this->assertTrue($product->hasEnoughStock(100));
        $this->assertFalse($product->hasEnoughStock(150));
    }
}
