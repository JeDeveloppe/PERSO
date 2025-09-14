<?php

namespace App\Service;

use App\Entity\EarlyRepayment;
use DateTimeZone;
use DateTimeImmutable;
use App\Entity\Investment;
use App\Repository\EarlyRepaymentRepository;
use App\Repository\InvestmentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class EarlyRepaymentService {
    
    public function __construct(
        private EarlyRepaymentRepository $earlyRepaymentRepository,
        private MathsService $mathsService,
        private InterestCalculationService $interestCalculationService,
        private EntityManagerInterface $em
    )
    {}

    public function calculateRemainingCapital(Investment $investment): int
    {

        $sum = $this->earlyRepaymentRepository->sumEarlyRepaymentsByInvestment($investment);

        //?on met à jour le capital recu par l'investisseur
        $investment->setCapitalAlreadyReceived($sum);

        $remainingCapital = $investment->getStartingCapital() - $sum;

        return $remainingCapital;
    }

    public function updateCapitalAlreadyReceived(Investment $investment): void
    {
        $investment->setCapitalAlreadyReceived($this->earlyRepaymentRepository->sumEarlyRepaymentsByInvestment($investment));
        $this->em->persist($investment);
        $this->em->flush();
    }

    public function calculateRemainingInterrestByMonth(Investment $investment, $remainingCapital): int
    {
        $rate = $investment->getRate();
        //?s'il y a des reglements anticipés, on prend le dernier capital calculé
        if($investment->getEarlyRepayments()->count() > 0) {
            $lastEarlyRepayment = $this->earlyRepaymentRepository->findLastEarlyRepayment($investment);
            $lastCapital = $lastEarlyRepayment->getRemainingCapital();
        }else{
            $lastCapital = $remainingCapital;
        }

        $newInterestByMonth = round($lastCapital * $this->mathsService->transformeRateIntoPercentage($rate) / 12, 2);
        return $newInterestByMonth;

    }

        /**
     * Met à jour le total des intérêts reçus dans l'entité Investment.
     * Cette méthode doit être appelée après chaque ajout ou modification d'un EarlyRepayment.
     *
     * @param Investment $investment L'investissement à mettre à jour.
     */
    public function updateTotalInterestReceived(Investment $investment): void
    {
        $totalInterests = $this->interestCalculationService->calculateTotalInterestReceived($investment);
        $investment->setTotalInterestReceived($totalInterests);

        $this->em->persist($investment);
        $this->em->flush();
    }
}