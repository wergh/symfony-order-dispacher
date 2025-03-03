<?php

declare(strict_types=1);

namespace App\Domain\Client\Entity;

use App\Domain\Order\Entity\Order;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Client entity representing a client in the system.
 *
 */
#[ORM\Entity]
class Client
{
    /**
     * The ID of the client.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    /**
     * The name of the client.
     *
     * @var string
     */
    #[ORM\Column(length: 255)]
    private string $name;

    /**
     * The surname of the client.
     *
     * @var string
     */
    #[ORM\Column(length: 255)]
    private string $surname;

    /**
     * The orders associated with the client.
     *
     * @var Collection
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'client')]
    private Collection $orders;

    /**
     * Client constructor.
     *
     * @param string $name The name of the client.
     * @param string $surname The surname of the client.
     */
    public function __construct(string $name, string $surname)
    {
        $this->orders = new ArrayCollection();
        $this->name = $name;
        $this->surname = $surname;
    }

    /**
     * Get the ID of the client.
     *
     * @return int|null The ID of the client.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the name of the client.
     *
     * @return string|null The name of the client.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of the client.
     *
     * @param string $name The name to set.
     *
     * @return static The current instance for method chaining.
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the surname of the client.
     *
     * @return string|null The surname of the client.
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * Set the surname of the client.
     *
     * @param string $surname The surname to set.
     *
     * @return static The current instance for method chaining.
     */
    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }
}
