<?php

namespace App\Service;

use DateTimeImmutable;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\InvestmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

class ChartService {
    
    public function __construct(
        private InvestmentRepository $investmentRepository,
        private EntityManagerInterface $entityManagerInterface,
        private ChartBuilderInterface $chartBuilder
    )
    {}

    public function generateChartInterests($investments): Chart
    {
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
        foreach($investments as $investment) {
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

        return $chart;
    }

}



// // Initialise les variables de calcul
// $total_deja_recu = 0;
// $max_mois_restant = 0;
// $total_par_mois = [];

// // Calcule le montant total déjà reçu et le nombre max de Durées
// foreach ($investissements as $investissement) {
//     $total_deja_recu += $investissement['Déjà reçu'];
//     $mois_restant = intval($investissement['Durée']);
//     if ($mois_restant > $max_mois_restant) {
//         $max_mois_restant = $mois_restant;
//     }
// }

// // Calcule les totaux pour chaque mois à venir, en ajoutant le capital pour le dernier mois
// for ($i = 1; $i <= $max_mois_restant; $i++) {
//     $total_par_mois[$i] = 0;
//     foreach ($investissements as $investissement) {
//         $mois_restant = intval($investissement['Durée']);
//         if ($i <= $mois_restant) {
//             // C'est le dernier mois de l'investissement
//             if ($i == $mois_restant) {
//                 $total_par_mois[$i] += $investissement['Montant / mois'] + $investissement['Capital'];
//             } else {
//                 // Ce n'est pas le dernier mois
//                 $total_par_mois[$i] += $investissement['Montant / mois'];
//             }
//         }
//     }
// }
