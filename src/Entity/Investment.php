<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InvestmentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvestmentRepository::class)]
class Investment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('investment:read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('investment:read')]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column]
    private ?int $startingCapital = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $rate = null;

    #[ORM\Column(nullable: true)]
    private ?int $alreadyReceived = null;

    /**
     * @var Collection<int, EarlyRepayment>
     */
    #[ORM\OneToMany(targetEntity: EarlyRepayment::class, mappedBy: 'investment')]
    #[Groups('investment:read')]
    private Collection $earlyRepayments;

    #[ORM\Column]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column]
    private ?int $interestByMonth = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1,
        max: 31,
        notInRangeMessage: 'Le nombre doit être compris entre {{ min }} et {{ max }}.',
    )]
    private ?int $paymentDate = null;

    #[ORM\Column]
    private ?bool $isFinished = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $buyAt = null;

    public function __construct()
    {
        $this->earlyRepayments = new ArrayCollection();
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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getStartingCapital(): ?int
    {
        return $this->startingCapital;
    }

    public function setStartingCapital(int $startingCapital): static
    {
        $this->startingCapital = $startingCapital;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getAlreadyReceived(): ?int
    {
        return $this->alreadyReceived;
    }

    public function setAlreadyReceived(?int $alreadyReceived): static
    {
        $this->alreadyReceived = $alreadyReceived;

        return $this;
    }

    /**
     * @return Collection<int, EarlyRepayment>
     */
    public function getEarlyRepayments(): Collection
    {
        return $this->earlyRepayments;
    }

    public function addEarlyRepayment(EarlyRepayment $earlyRepayment): static
    {
        if (!$this->earlyRepayments->contains($earlyRepayment)) {
            $this->earlyRepayments->add($earlyRepayment);
            $earlyRepayment->setInvestment($this);
        }

        return $this;
    }

    public function removeEarlyRepayment(EarlyRepayment $earlyRepayment): static
    {
        if ($this->earlyRepayments->removeElement($earlyRepayment)) {
            // set the owning side to null (unless already changed)
            if ($earlyRepayment->getInvestment() === $this) {
                $earlyRepayment->setInvestment(null);
            }
        }

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getInterestByMonth(): ?int
    {
        return $this->interestByMonth;
    }

    public function setInterestByMonth(int $interestByMonth): static
    {
        $this->interestByMonth = $interestByMonth;

        return $this;
    }

    public function getPaymentDate(): ?int
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(int $paymentDate): static
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getRemainingMonths(): int|string
    {
        $now = new \DateTimeImmutable();
        if ($this->endAt === null) {
            return 'N/A';
        }

        if ($this->endAt < $now) {
            return 'Terminé';
        }

        $interval = $this->endAt->diff($now);
        return ($interval->y * 12) + $interval->m;
    }

    public function isFinished(): ?bool
    {
        return $this->isFinished;
    }

    public function setIsFinished(bool $isFinished): static
    {
        $this->isFinished = $isFinished;

        return $this;
    }

    public function getBuyAt(): ?\DateTimeImmutable
    {
        return $this->buyAt;
    }

    public function setBuyAt(\DateTimeImmutable $buyAt): static
    {
        $this->buyAt = $buyAt;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
