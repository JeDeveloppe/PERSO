<?php

namespace App\Service;

use DateTimeImmutable;
use App\Entity\EarlyRepayment;
use App\Repository\EarlyRepaymentRepository;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\InvestmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

class ChartService {
    
    public function __construct(
        private InvestmentRepository $investmentRepository,
        private EntityManagerInterface $entityManagerInterface,
        private ChartBuilderInterface $chartBuilder,
        private EarlyRepaymentRepository $earlyRepaymentRepository
    )
    {}

    public function generateChartInterests($investments): Chart
    {
        $plotData = [];
        $allDates = [];
        
        // Définir la plage de dates du graphique
        $now = new DateTimeImmutable();
        $startDate = $now->modify('-1 months');
        $endDate = $startDate->modify('+24 months');

        $interval = new \DateInterval('P1M');
        $period = new \DatePeriod($startDate, $interval, $endDate);

        foreach ($period as $date) {
            $key = $date->format('Y-m');
            $allDates[$key] = $key;
        }

        // Prépare les données pour le graphique à barres empilées
        foreach($investments as $investment) {
            $investmentStartDate = $investment->getStartAt();
            $duration = $investment->getDuration();
            $interestByMonth = $investment->getInterestByMonth(); // Valeur en centimes
            $investmentName = $investment->getName();
            
            if (!$investmentStartDate) {
                continue;
            }

            $currentInvestmentData = [];
            
            // Récupère le dernier remboursement anticipé
            $lastEarlyRepayment = $this->earlyRepaymentRepository->findLastEarlyRepayment($investment);
            
            // Boucle sur les mois de l'investissement
            for ($i = 0; $i < $duration; $i++) {
                $date = $investmentStartDate->modify('+' . $i . ' months');
                $key = $date->format('Y-m');
                
                // Si un remboursement anticipé existe
                if ($lastEarlyRepayment) {
                    // Si la date du mois en cours est après la date du remboursement
                    if ($date > $lastEarlyRepayment->getCreatedAt()->modify('last day of this month')) {
                        // Utiliser le nouvel intérêt restant par mois
                        $currentInvestmentData[$key] = $lastEarlyRepayment->getRemainingInterestByMonth();
                    } else {
                        // Utiliser l'intérêt initial
                        $currentInvestmentData[$key] = $interestByMonth;
                    }
                } else {
                    // Aucun remboursement anticipé, utiliser l'intérêt initial
                    $currentInvestmentData[$key] = $interestByMonth;
                }
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
                // Conversion finale en euros pour le graphique
                $data[] = isset($monthlyData[$month]) ? $monthlyData[$month] / 100 : 0;
            }
            $datasets[] = [
                'label' => $investmentName,
                'data' => $data,
                'backgroundColor' => $colors[$i % count($colors)],
            ];
            $i++;
        }

        // --- Création des annotations horizontales ---
        $annotations = [];
        $lineId = 0;
        $currentTotals = array_fill_keys($labels, 0);

        foreach ($datasets as $dataset) {
            foreach ($dataset['data'] as $index => $value) {
                $month = $labels[$index];
                $currentTotals[$month] += $value; // Cumule la valeur pour cette barre
                
                // Crée une ligne horizontale si la valeur n'est pas zéro
                if ($value > 0) {
                    $annotations['line' . $lineId] = [
                        'type' => 'line',
                        'mode' => 'horizontal',
                        'yMin' => $currentTotals[$month],
                        'yMax' => $currentTotals[$month],
                        'xMin' => $month, // Ancre la ligne au mois
                        'xMax' => $month,
                        'borderColor' => 'rgba(0, 0, 0, 0.2)',
                        'borderWidth' => 1,
                        'borderDash' => [2, 2],
                    ];
                    $lineId++;
                }
            }
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
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
                'annotation' => [
                    'annotations' => $annotations,
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

        return $chart;
    }
}