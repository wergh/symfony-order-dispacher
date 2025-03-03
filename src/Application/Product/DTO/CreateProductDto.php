<?php

declare(strict_types=1);

namespace App\Application\Product\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateProductDto
{


    #[Assert\Type('string')]
    #[Assert\NotBlank(message: 'El nombre no puede estar en blanco.')]
    public string $name;

    #[Assert\Type('float')]
    #[Assert\Positive(message: 'El precio debe ser un número positivo.')]
    public float $price;

    #[Assert\Type('int')]
    #[Assert\PositiveOrZero(message: 'El impuesto debe ser un número positivo o cero.')]
    public int $tax;

    #[Assert\Type('int')]
    #[Assert\Positive(message: 'El stock debe ser un número positivo.')]
    public int $stock;

    /**
     * CreateProductDto constructor.
     *
     * @param string $name The name of the product.
     * @param float  $price The price of the product.
     * @param int    $tax The tax rate of the product.
     * @param int    $stock The stock quantity of the product.
     */
    public function __construct(
        string $name,
        float  $price,
        int    $tax,
        int    $stock,
    )
    {
        $this->name = $name;
        $this->price = $price;
        $this->tax = $tax;
        $this->stock = $stock;
    }
}
