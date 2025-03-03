<?php

declare(strict_types=1);

namespace App\Application\Order\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class OrderDTO
{
    #[Assert\NotBlank(message: 'El cliente no puede estar vacío.')]
    #[Assert\Positive(message: 'El ID del cliente debe ser positivo.')]
    public int $clientId;

    #[Assert\NotBlank(message: 'La orden debe tener al menos un concepto.')]
    #[Assert\Valid]
    public array $concepts;

    /**
     * @param int $clientId
     * @param OrderConceptDTO[] $concepts
     */
    public function __construct(int $clientId, array $concepts)
    {
        $this->clientId = $clientId;
        $this->concepts = $concepts;
    }
}
