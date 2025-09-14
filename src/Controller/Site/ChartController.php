<?php

namespace App\Controller\Site;

use App\Service\ChartService;
use App\Repository\InvestmentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class ChartController extends AbstractController
{
    public function __construct(
        private InvestmentRepository $investmentRepository,
        private ChartService $chartService
    ) {}

    #[Route('/investments/interests-projection/', name: 'app_investments_interests_projection')]
    public function chartOfInvestmentsInterestsProjection(): Response
    {
        $investments = $this->investmentRepository->findBy(['isFinished' => false], ['buyAt' => 'DESC']);

        $chart = $this->chartService->generateChartInterestsByInvestment($investments);
        
        return $this->render('site/chart/investment/interest_projection.html.twig', [
            'chart' => $chart,
        ]);
    }

    #[Route('/accounts/soldes-projection/', name: 'app_accounts_soldes_projection')]
    public function chartOfAccountsSoldes(): Response
    {
        $chart = $this->chartService->generateChartInterestByAccount();

        return $this->render('site/chart/account/soldes_projection.html.twig', [
            'chart' => $chart,
        ]);
    }

    #[Route('/accounts/interests-projection/', name: 'app_accounts_interests_projection')]
    public function chartOfAccountInterestsProjection(): Response
    {
        $chart = $this->chartService->generateChartInterestByAcccountByYear();

        return $this->render('site/chart/account/interests_projection.html.twig', [
            'chart' => $chart,
        ]);
    }
}
