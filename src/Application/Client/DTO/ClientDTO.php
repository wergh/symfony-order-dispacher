<?php

declare(strict_types=1);

namespace App\Application\Client\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ClientDTO
{

    #[Assert\Type('string')]
    #[Assert\NotBlank(message: 'El nombre no puede estar en blanco.')]
    public string $name;

    #[Assert\Type('string')]
    #[Assert\NotBlank(message: 'El apellido no puede estar en blanco.')]
    public string $surname;

    public function __construct(string $name, string $surname)
    {

        $this->name = $name;
        $this->surname = $surname;

    }
}
