<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $value = null;

    #[ORM\Column]
    private ?bool $isAvailable = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bank $bank = null;

    /**
     * @var Collection<int, Ceiling>
     */
    #[ORM\ManyToMany(targetEntity: Ceiling::class, inversedBy: 'accounts')]
    private Collection $ceiling;

    public function __construct()
    {
        $this->ceiling = new ArrayCollection();
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

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getBank(): ?Bank
    {
        return $this->bank;
    }

    public function setBank(?Bank $bank): static
    {
        $this->bank = $bank;

        return $this;
    }

    /**
     * @return Collection<int, Ceiling>
     */
    public function getCeiling(): Collection
    {
        return $this->ceiling;
    }

    public function addCeiling(Ceiling $ceiling): static
    {
        if (!$this->ceiling->contains($ceiling)) {
            $this->ceiling->add($ceiling);
        }

        return $this;
    }

    public function removeCeiling(Ceiling $ceiling): static
    {
        $this->ceiling->removeElement($ceiling);

        return $this;
    }
}
