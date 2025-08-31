<?php

namespace App\Service;

use DateTimeImmutable;
use App\Repository\InvestmentRepository;

class MathService {
    
    public function __construct(
        private InvestmentRepository $investmentRepository,
    )
    {}

    /**
     * Calcule le capital total investi à partir de tous les investissements.
     */
    public function calculateCapitalInvested(): int
    {
        $capitalInvested = 0;
        $investments = $this->investmentRepository->findAll();

        foreach ($investments as $investment) {
            $capitalInvested += $investment->getStartingCapital();
        }

        return $capitalInvested;
    }

    /**
     * Calcule le nombre de mois restants entre la date de fin de l'investissement et aujourd'hui.
     */
    public function calculateRemainingMonths(DateTimeImmutable $endAt): int|string
    {
        $now = new DateTimeImmutable();
        $interval = $endAt->diff($now);

        if ($interval->invert) {
            return 'Terminé';
        }

        return ($interval->y * 12) + $interval->m;
    }
}
