<?php

declare(strict_types=1);

namespace App\Application\Client\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for client creation.
 */
final class ClientCreateDto
{
    /**
     * Client's first name.
     *
     * @var string
     */
    #[Assert\Type('string')]
    #[Assert\NotBlank(message: 'El nombre no puede estar en blanco.')]
    public string $name;

    /**
     * Client's last name.
     *
     * @var string
     */
    #[Assert\Type('string')]
    #[Assert\NotBlank(message: 'El apellido no puede estar en blanco.')]
    public string $surname;

    /**
     * Class constructor.
     *
     * @param string $name    Client's first name.
     * @param string $surname Client's last name.
     */
    public function __construct(string $name, string $surname)
    {
        $this->name = $name;
        $this->surname = $surname;
    }
}
