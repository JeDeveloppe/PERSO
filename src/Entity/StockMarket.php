<?php

namespace App\Entity;

use App\Repository\StockMarketRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockMarketRepository::class)]
class StockMarket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $purchasePriceIncludingTVA = null;

    #[ORM\Column(length: 255)]
    private ?string $isinCode = null;

    #[ORM\Column(length: 255)]
    private ?string $symbol = null;

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPurchasePriceIncludingTVA(): ?int
    {
        return $this->purchasePriceIncludingTVA;
    }

    public function setPurchasePriceIncludingTVA(int $purchasePriceIncludingTVA): static
    {
        $this->purchasePriceIncludingTVA = $purchasePriceIncludingTVA;

        return $this;
    }

    public function getIsinCode(): ?string
    {
        return $this->isinCode;
    }

    public function setIsinCode(string $isinCode): static
    {
        $this->isinCode = $isinCode;

        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = $symbol;

        return $this;
    }
}
