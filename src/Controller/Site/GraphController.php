<?php

namespace App\Controller\Site;

use App\Repository\InvestmentRepository;
use App\Service\MathService;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use DateTimeImmutable;


final class GraphController extends AbstractController
{
    public function __construct(
        private InvestmentRepository $investmentRepository,
        private MathService $mathService
    ) {}

    #[Route('/investments/graph/', name: 'app_jp_graph')]
    public function index(ChartBuilderInterface $chartBuilder): Response
    {
        $investments = $this->investmentRepository->findAll();
        $plotData = [];
        $allDates = [];
        
        // Définir la plage de dates du graphique
        $now = new DateTimeImmutable();
        $startDate = $now->modify('-2 months');
        $endDate = $now->modify('+24 months'); // Pour afficher 24 mois après la date actuelle par défaut

        $interval = new \DateInterval('P1M');
        $period = new \DatePeriod($startDate, $interval, $endDate);

        foreach ($period as $date) {
            $key = $date->format('Y-m');
            $allDates[$key] = $key;
        }

        // Prépare les données pour le graphique à barres empilées
        foreach ($investments as $investment) {
            $investmentStartDate = $investment->getStartAt();
            $duration = $investment->getDuration();
            $interestByMonth = $investment->getInterestByMonth() / 100;
            $investmentName = $investment->getName();
            
            if (!$investmentStartDate) {
                continue;
            }

            $currentInvestmentData = [];
            
            // Boucle sur les mois de l'investissement
            for ($i = 0; $i < $duration; $i++) {
                $date = $investmentStartDate->modify('+' . $i . ' months');
                $key = $date->format('Y-m');
                
                $currentInvestmentData[$key] = $interestByMonth;
            }

            $plotData[$investmentName] = $currentInvestmentData;
        }

        ksort($allDates);
        $labels = array_keys($allDates);
        
        $datasets = [];
        $colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6610f2', '#6c757d', '#fd7e14', '#e83e8c', '#20c997', '#007bff'];
        $i = 0;
        foreach ($plotData as $investmentName => $monthlyData) {
            $data = [];
            foreach ($labels as $month) {
                $data[] = $monthlyData[$month] ?? 0;
            }
            $datasets[] = [
                'label' => $investmentName,
                'data' => $data,
                'backgroundColor' => $colors[$i % count($colors)],
            ];
            $i++;
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => $datasets,
        ]);

        $chart->setOptions([
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Revenu mensuel total par investissement',
                    'font' => [
                        'size' => 16,
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'responsive' => true,
            'scales' => [
                'x' => [
                    'stacked' => true,
                    'ticks' => [
                        'font' => [
                            'size' => 10,
                        ],
                    ],
                    // Largeur de la barre
                    'categoryPercentage' => 0.8,
                    'barPercentage' => 0.9,
                ],
                'y' => [
                    'stacked' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Montant en EUR',
                    ],
                ],
            ],
        ]);
        
        return $this->render('site/chart/chart.html.twig', [
            'chart' => $chart,
        ]);
    }
}
