<?php

namespace App\Service;

use App\Entity\EarlyRepayment;
use DateTimeZone;
use DateTimeImmutable;
use App\Entity\Investment;
use App\Repository\EarlyRepaymentRepository;
use App\Repository\InvestmentRepository;
use Doctrine\ORM\EntityManagerInterface;

class EarlyRepaymentService {
    
    public function __construct(
        private EarlyRepaymentRepository $earlyRepaymentRepository,
        private MathsService $mathsService
    )
    {}

    public function calculateRemainingCapital(Investment $investment): int
    {

        $sum = $this->earlyRepaymentRepository->sumEarlyRepaymentsByInvestment($investment);
        $remainingCapital = $investment->getStartingCapital() - $sum;

        return $remainingCapital;
    }

    public function calculateRemainingInterrestByMonth(Investment $investment): int
    {
        $rate = $investment->getRate();

        //?s'il y a des reglements anticipés, on prend le dernier capital calculé
        if($investment->getEarlyRepayments()->count() > 0) {
            $lastEarlyRepayment = $this->earlyRepaymentRepository->findLastEarlyRepayment($investment);
            $lastCapital = $lastEarlyRepayment->getRemainingCapital();
        }else{
            $lastCapital = $investment->getStartingCapital();
        }

        $newInterestByMonth = round($lastCapital * $this->mathsService->transformeRateIntoPercentage($rate) / 12, 2);

        return $newInterestByMonth;

    }

}