<?php

namespace App\Entity;

use App\Repository\BeerGlassRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BeerGlassRepository::class)]
class BeerGlass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array $complexState = [];

    #[ORM\Column(nullable: true)]
    private ?int $amount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getComplexState(): array
    {
        return $this->complexState;
    }

    public function setComplexState(?array $complexState): self
    {
        $this->complexState = $complexState;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
