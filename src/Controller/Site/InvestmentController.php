<?php

namespace App\Controller\Site;

use App\Service\MathService;
use App\Service\InvestmentService;
use App\Repository\InvestmentRepository;
use App\Service\EarlyRepaymentService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class InvestmentController extends AbstractController
{
    public function __construct(
        private InvestmentService $investmentService,
        private InvestmentRepository $investmentRepository,
        private EarlyRepaymentService $earlyRepaymentService,
        private SerializerInterface $serializer
    )
    {
    }

    #[Route('/investments', name: 'app_investments_ongoing')]
    public function index(InvestmentRepository $investmentRepository, SerializerInterface $serializer): Response
    {
        $allInvestments = $investmentRepository->findAll();

        //quelques totaux
        $capitalInvested = $this->investmentService->calculateCapitalInvested();
        $interestReceived = $this->investmentService->calculateInterestReceived();
        $annuelYielFromAllInvestmentsWithInterest = $this->investmentService->calculateAnnualizedRendementFromAllInvestmentsWithInterest($allInvestments);
        $averageRateFromAllInvestmentsWithInterest = $this->investmentService->calculateAverageRateFromAllInvestmentsWithInterest($allInvestments);

        $ongoingInvestments = array_filter($allInvestments, function($investment) {
            return $investment->getRemainingMonths() !== 'Terminé';
        });

        usort($ongoingInvestments, function($a, $b) {
            return $a->getRemainingMonths() <=> $b->getRemainingMonths();
        });

        // Sérialisez le tableau d'objets pour le JavaScript
        $ongoingInvestmentsJson = $serializer->serialize($ongoingInvestments, 'json', ['groups' => 'investment:read']);

        return $this->render('site/investments/ongoing.html.twig', [
            'investments' => $ongoingInvestments, // Pour la boucle Twig
            'investmentsJson' => $ongoingInvestmentsJson, // Pour le JavaScript
            'capitalInvested' => $capitalInvested,
            'interestReceived' => $interestReceived,
            'annuelYielFromAllInvestmentsWithInterest' => $annuelYielFromAllInvestmentsWithInterest,
            'averageRateFromAllInvestmentsWithInterest' => $averageRateFromAllInvestmentsWithInterest
        ]);
    }

    #[Route('/investments/finished', name: 'app_investments_finished')]
    public function finished(): Response
    {
        $allInvestments = $this->investmentRepository->findAll();
        $capitalInvested = $this->investmentService->calculateCapitalInvested();

        $finishedInvestments = array_filter($allInvestments, function($investment) {
            return $investment->getRemainingMonths() === 'Terminé';
        });
        
        return $this->render('site/investments/finished.html.twig', [
            'investments' => $finishedInvestments,
            'capitalInvested' => $capitalInvested,
        ]);
    }
}
