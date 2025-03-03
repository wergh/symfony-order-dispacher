<?php

declare(strict_types=1);

namespace App\Application\Order\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object (DTO) for creating an order.
 */
final class CreateOrderDto
{
    /**
     * Client's id
     *
     * @var int
     */
    #[Assert\NotBlank(message: 'El cliente no puede estar vacío.')]
    #[Assert\Positive(message: 'El ID del cliente debe ser positivo.')]
    public int $clientId;

    /**
     * he concepts or items included in the order.
     *
     * @var OrderConceptDto[]
     */
    #[Assert\NotBlank(message: 'La orden debe tener al menos un concepto.')]
    #[Assert\Valid]
    public array $concepts;

    /**
     * CreateOrderDto constructor.
     *
     * @param int $clientId The ID of the client associated with the order.
     * @param OrderConceptDto[] $concepts The concepts or items included in the order.
     */
    public function __construct(int $clientId, array $concepts)
    {
        $this->clientId = $clientId;
        $this->concepts = $concepts;
    }
}
