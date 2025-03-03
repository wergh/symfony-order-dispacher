<?php

declare(strict_types=1);

namespace App\Application\Order\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object (DTO) for representing an order concept (product and quantity).
 */
final class OrderConceptDto
{
    #[Assert\NotBlank(message: 'El producto no puede estar vacío.')]
    #[Assert\Positive(message: 'El ID del producto debe ser positivo.')]
    public int $productId;

    #[Assert\NotBlank(message: 'La cantidad no puede estar vacía.')]
    #[Assert\Positive(message: 'La cantidad debe ser positiva.')]
    public int $quantity;

    /**
     * OrderConceptDto constructor.
     *
     * @param int $productId The ID of the product in the order concept.
     * @param int $quantity The quantity of the product in the order concept.
     */
    public function __construct(int $productId, int $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }
}
