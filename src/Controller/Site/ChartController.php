<?php

namespace App\Controller\Site;

use App\Service\MathService;
use App\Service\ChartService;
use App\Repository\InvestmentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class ChartController extends AbstractController
{
    public function __construct(
        private InvestmentRepository $investmentRepository,
        private MathService $mathService,
        private ChartService $chartservice
    ) {}

    #[Route('/investments/graph/', name: 'app_jp_graph')]
    public function index(): Response
    {
        $investments = $this->investmentRepository->findAll();

        $chart = $this->chartservice->generateChartInterests($investments);
        
        return $this->render('site/chart/chart.html.twig', [
            'chart' => $chart,
        ]);
    }
}
