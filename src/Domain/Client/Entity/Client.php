<?php

declare(strict_types=1);

namespace App\Domain\Client\Entity;

use App\Domain\Order\Entity\Order;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $surname;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'client')]
    private Collection $orders;

    public function __construct(string $name, string $surname)
    {
        $this->orders = new ArrayCollection();
        $this->name = $name;
        $this->surname = $surname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }
}
