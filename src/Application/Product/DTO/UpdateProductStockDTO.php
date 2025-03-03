<?php

declare(strict_types=1);

namespace App\Application\Product\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateProductStockDTO
{


    #[Assert\Type('int')]
    #[Assert\Positive(message: 'Los Ids siempre son positivos')]
    public int $id;

    #[Assert\Type('int')]
    #[Assert\Positive(message: 'El stock debe ser un número positivo.')]
    public int $stock;

    /**
     * UpdateProductStockDTO constructor.
     *
     * @param int $id The product ID.
     * @param int $stock The stock quantity to update.
     */
    public function __construct(
        int $id,
        int $stock,
    )
    {
        $this->id = $id;
        $this->stock = $stock;
    }
}
