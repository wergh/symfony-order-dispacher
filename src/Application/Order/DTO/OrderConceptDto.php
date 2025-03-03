<?php

declare(strict_types=1);

namespace App\Application\Order\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class OrderConceptDto
{
    #[Assert\NotBlank(message: 'El producto no puede estar vacío.')]
    #[Assert\Positive(message: 'El ID del producto debe ser positivo.')]
    public int $productId;

    #[Assert\NotBlank(message: 'La cantidad no puede estar vacía.')]
    #[Assert\Positive(message: 'La cantidad debe ser positiva.')]
    public int $quantity;

    public function __construct(int $productId, int $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }
}
