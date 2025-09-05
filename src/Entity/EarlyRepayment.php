<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EarlyRepaymentRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EarlyRepaymentRepository::class)]
class EarlyRepayment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('investment:read')]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups('investment:read')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups('investment:read')]
    private ?int $value = null;

    #[ORM\ManyToOne(inversedBy: 'earlyRepayments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Investment $investment = null;

    #[ORM\Column]
    #[Groups('investment:read')]
    private ?int $remainingCapital = null;

    #[ORM\Column]
    #[Groups('investment:read')]
    private ?int $remainingInterestByMonth = null;

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

    public function getRemainingCapital(): ?int
    {
        return $this->remainingCapital;
    }

    public function setRemainingCapital(int $remainingCapital): static
    {
        $this->remainingCapital = $remainingCapital;

        return $this;
    }

    public function getRemainingInterestByMonth(): ?int
    {
        return $this->remainingInterestByMonth;
    }

    public function setRemainingInterestByMonth(int $remainingInterestByMonth): static
    {
        $this->remainingInterestByMonth = $remainingInterestByMonth;

        return $this;
    }
}
