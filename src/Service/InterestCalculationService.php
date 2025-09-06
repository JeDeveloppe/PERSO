<?php

namespace App\Service;

use App\Entity\Investment;
use App\Repository\EarlyRepaymentRepository;
use App\Service\MathsService;
use DateTimeImmutable;

class InterestCalculationService
{
    public function __construct(
        private EarlyRepaymentRepository $earlyRepaymentRepository,
        private MathsService $mathsService
    ) {
    }

    /**
     * Calcule le total des intérêts reçus pour un investissement, en prenant en compte les remboursements anticipés.
     * Le calcul est basé sur le capital restant après chaque remboursement.
     *
     * @param Investment $investment L'objet Investment pour lequel on calcule les intérêts.
     * @return int Le total des intérêts reçus, en centimes d'euro.
     */
    public function calculateTotalInterestReceived(Investment $investment): int
    {
        // Récupère tous les remboursements anticipés pour l'investissement, triés par date
        $earlyRepayments = $this->earlyRepaymentRepository->findBy(['investment' => $investment], ['createdAt' => 'ASC']);

        $totalInterestReceived = 0;
        $lastCalculationDate = $investment->getStartAt();
        $currentCapital = $investment->getStartingCapital();
        $rate = $investment->getRate();
        $percentageRate = $this->mathsService->transformeRateIntoPercentage($rate);

        // Itère sur les remboursements anticipés pour calculer les intérêts cumulés sur chaque période
        foreach ($earlyRepayments as $repayment) {
            $repaymentDate = $repayment->getCreatedAt();

            // Calcule la différence en mois entre la dernière date de calcul et la date du remboursement
            $interval = $lastCalculationDate->diff($repaymentDate);
            $monthsPassed = $interval->y * 12 + $interval->m;
            
            // Calcule l'intérêt mensuel pour cette période avec le capital actuel
            $monthlyInterest = round($currentCapital * $percentageRate / 12, 2);
            $totalInterestReceived += $monthsPassed * $monthlyInterest;
            
            // Met à jour le capital et la dernière date de calcul pour la période suivante
            $currentCapital -= $repayment->getValue();
            $lastCalculationDate = $repaymentDate;
        }

        // Calcule les intérêts pour la période entre le dernier remboursement et la date actuelle
        $today = new DateTimeImmutable();
        $interval = $lastCalculationDate->diff($today);
        $monthsPassed = $interval->y * 12 + $interval->m;

        // Calcule le taux d'intérêt mensuel pour la période finale avec le capital restant
        $monthlyInterest = round($currentCapital * $percentageRate / 12, 2);
        $totalInterestReceived += $monthsPassed * $monthlyInterest;

        return $totalInterestReceived;
    }
}
