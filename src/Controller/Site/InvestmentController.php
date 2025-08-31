<?php

namespace App\Controller\Site;

use App\Service\MathService;
use App\Repository\InvestmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvestmentController extends AbstractController
{
    #[Route('/investments', name: 'app_investments_ongoing')]
    public function index(MathService $mathService, InvestmentRepository $investmentRepository): Response
    {
        $allInvestments = $investmentRepository->findAll();
        $capitalInvested = $mathService->calculateCapitalInvested();

        $ongoingInvestments = array_filter($allInvestments, function($investment) {
            return $investment->getRemainingMonths() !== 'Terminé';
        });

        usort($ongoingInvestments, function($a, $b) {
            return $a->getRemainingMonths() <=> $b->getRemainingMonths();
        });

        return $this->render('site/investments/ongoing.html.twig', [
            'investments' => $ongoingInvestments,
            'capitalInvested' => $capitalInvested,
        ]);
    }

    #[Route('/investments/finished', name: 'app_investments_finished')]
    public function finished(MathService $mathService, InvestmentRepository $investmentRepository): Response
    {
        $allInvestments = $investmentRepository->findAll();
        $capitalInvested = $mathService->calculateCapitalInvested();

        $finishedInvestments = array_filter($allInvestments, function($investment) {
            return $investment->getRemainingMonths() === 'Terminé';
        });
        
        return $this->render('site/investments/finished.html.twig', [
            'investments' => $finishedInvestments,
            'capitalInvested' => $capitalInvested,
        ]);
    }
}
