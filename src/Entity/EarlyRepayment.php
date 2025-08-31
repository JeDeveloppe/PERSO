<?php

namespace App\Entity;

use App\Repository\EarlyRepaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EarlyRepaymentRepository::class)]
class EarlyRepayment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?int $value = null;

    #[ORM\ManyToOne(inversedBy: 'earlyRepayments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Investment $investment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    public function getInvestment(): ?Investment
    {
        return $this->investment;
    }

    public function setInvestment(?Investment $investment): static
    {
        $this->investment = $investment;

        return $this;
    }
}
