<?php

namespace App\Service;

use DateTimeZone;
use DateTimeImmutable;
use App\Entity\Investment;
use App\Repository\InvestmentRepository;
use Doctrine\ORM\EntityManagerInterface;

class InvestmentService {
    
    public function __construct(
        private InvestmentRepository $investmentRepository,
        private EntityManagerInterface $entityManagerInterface
    )
    {}

    public function initaliseInvestments() {
        $investments = [
            [
                'Nom' => 'Duplex Cros de Cagnes',
                'Date' => '18/01/2025',
                'Capital' => 1000,
                'Déjà reçu' => 44,
                'Taux' => 9,
                'Montant / mois' => 8,
                'Durée' => '36'
            ],
            [
                'Nom' => 'Les Cabanes Parisiennes',
                'Date' => '23/10/2023',
                'Capital' => 1000,
                'Déjà reçu' => 118,
                'Taux' => 7,
                'Montant / mois' => 6,
                'Durée' => '120'
            ],
            [
                'Nom' => 'Hôtel Bordeaux Lormont',
                'Date' => '23/10/2023',
                'Capital' => 1000,
                'Déjà reçu' => 124,
                'Taux' => 9,
                'Montant / mois' => 7,
                'Durée' => '48'
            ],
            [
                'Nom' => 'Villa Terres Blanches Tourrettes',
                'Date' => '20/08/2025',
                'Capital' => 2000,
                'Déjà reçu' => 48,
                'Taux' => 11,
                'Montant / mois' => 18,
                'Durée' => '24'
            ],
            [
                'Nom' => 'Hôtel Croix de Malte Lourdes',
                'Date' => '07/05/2025',
                'Capital' => 2000,
                'Déjà reçu' => 36,
                'Taux' => 10,
                'Montant / mois' => 17,
                'Durée' => '18'
            ],
            [
                'Nom' => 'Immeuble Henri Martin Vanves',
                'Date' => '20/08/2025',
                'Capital' => 2000,
                'Déjà reçu' => 0,
                'Taux' => 9,
                'Montant / mois' => 0,
                'Durée' => '12'
            ],
            [
                'Nom' => 'Hôtel Villa Claudia Cannes',
                'Date' => '24/07/2025',
                'Capital' => 2000,
                'Déjà reçu' => 0,
                'Taux' => 10,
                'Montant / mois' => 0,
                'Durée' => '24'
            ],
            [
                'Nom' => 'Appartements Bandol Camas',
                'Date' => '10/07/2025',
                'Capital' => 2000,
                'Déjà reçu' => 3,
                'Taux' => 10,
                'Montant / mois' => 16,
                'Durée' => '12'
            ],
            [
                'Nom' => 'Manoir Narbonne',
                'Date' => '23/04/2025',
                'Capital' => 1456,
                'Déjà reçu' => 49,
                'Taux' => 11,
                'Montant / mois' => 13,
                'Durée' => '12'
            ],
            [
                'Nom' => 'Immeuble Jean Jaurès Vichy',
                'Date' => '14/05/2025',
                'Capital' => 2000,
                'Déjà reçu' => 33,
                'Taux' => 10,
                'Montant / mois' => 16,
                'Durée' => '18'
            ],
            [
                'Nom' => 'Division Blanquefort',
                'Date' => '12/08/2025',
                'Capital' => 2000,
                'Déjà reçu' => 0,
                'Taux' => 10,
                'Montant / mois' => 0,
                'Durée' => '7'
            ],
            [
                'Nom' => '16 Appartements Tourcoing',
                'Date' => '04/06/2025',
                'Capital' => 2000,
                'Déjà reçu' => 23,
                'Taux' => 10,
                'Montant / mois' => 17,
                'Durée' => '18'
            ],
            [
                'Nom' => 'Villa La Provençale Antibes',
                'Date' => '30/07/2025',
                'Capital' => 2000,
                'Déjà reçu' => 0,
                'Taux' => 9,
                'Montant / mois' => 0,
                'Durée' => '12'
            ],
            [
                'Nom' => 'Division Le Tampon Réunion',
                'Date' => '14/08/2025',
                'Capital' => 2000,
                'Déjà reçu' => 0,
                'Taux' => 10,
                'Montant / mois' => 0,
                'Durée' => '12'
            ],
            [
                'Nom' => 'Maison Eperon Réunion',
                'Date' => '29/05/2025',
                'Capital' => 2000,
                'Déjà reçu' => 27,
                'Taux' => 10,
                'Montant / mois' => 17,
                'Durée' => '12'
            ],
            [
                'Nom' => 'Immeuble Bidart Choko Gochoa',
                'Date' => '25/06/2025',
                'Capital' => 2000,
                'Déjà reçu' => 10,
                'Taux' => 10,
                'Montant / mois' => 16,
                'Durée' => '24'
            ]
        ];

        foreach($investments as $investment) {
        
            $entityInDatabase = $this->investmentRepository->findOneBy(['name' => $investment['Nom']]);
            if(!$entityInDatabase) {
                $entityInDatabase = new Investment();
            }

            // Correction de l'erreur : Utilisation de createFromFormat() pour analyser le format jj/mm/aaaa
            $startDate = null;
            if (!empty($investment['Date'])) {
                $startDate = DateTimeImmutable::createFromFormat('d/m/Y', $investment['Date'], new DateTimeZone('Europe/Paris'));
            }
            if($startDate === null) {
                // Gérer l'erreur si le format de la date est incorrect.
                // Par exemple, on peut ignorer l'investissement, ou enregistrer un log d'erreur.
                $startDate = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));
                continue;
            }

            $paymentDay = 5;
            //? On s'assure que le fuseau horaire est bien défini avant de manipuler la date
            $timezone = new DateTimeZone('Europe/Paris');
            $dateFin = DateTimeImmutable::createFromFormat('d/m/Y', $investment['Date'], new DateTimeZone('Europe/Paris'));

            
            $dateFin->setTimezone($timezone)
                ->modify('+' . $investment['Durée'] . ' months')
                ->setDate(
                    $startDate->modify('+' . $investment['Durée'] . ' months')->format('Y'),
                    $startDate->modify('+' . $investment['Durée'] . ' months')->format('m'),
                    $paymentDay
                );

            $entityInDatabase->setName($investment['Nom']);
            $entityInDatabase->setStartAt($startDate);
            $entityInDatabase->setBuyAt($startDate);
            $entityInDatabase->setEndAt($dateFin);
            $entityInDatabase->setIsFinished(false);
            $entityInDatabase->setTotalInterestReceived(0);
            $entityInDatabase->setStartingCapital($investment['Capital']);
            $entityInDatabase->setDuration($investment['Durée']);
            $entityInDatabase->setRate($investment['Taux']);
            $entityInDatabase->setInterestByMonth($investment['Montant / mois']);
            $entityInDatabase->setPaymentDate(5);

            $this->entityManagerInterface->persist($entityInDatabase);
        }

        $this->entityManagerInterface->flush();
    }

     public function calculateCapitalInvested(): int
    {
        $capitalInvested = 0;
        $investments = $this->investmentRepository->findAll();

        foreach ($investments as $investment) {
            $capitalInvested += $investment->getStartingCapital();
        }

        return $capitalInvested;
    }

    /**
     * Calcul des intérêts reçus
     */
    public function calculateInterestReceived(): int
    {
        $interestReceived = 0;
        $investments = $this->investmentRepository->findAll();

        foreach ($investments as $investment) {
            $interestReceived += $investment->getTotalInterestReceived();
        }

        return $interestReceived;
    }


    /**
     * Calcule le nombre de mois restants entre la date de fin de l'investissement et aujourd'hui.
     */
    public function calculateRemainingMonths(DateTimeImmutable $endAt): int|string
    {
        $now = new DateTimeImmutable();
        $interval = $endAt->diff($now);

        if ($interval->invert) {
            return 'Terminé';
        }

        return ($interval->y * 12) + $interval->m;
    }

    /**
     * Calcule le rendement annualisé basé sur le capital investi et les intérêts reçus.
     *
     * @param array $allInvestments L'ensemble des investissements à filtrer.
     * @return float Le rendement annualisé en pourcentage.
     */
    public function calculateAnnualizedRendementFromAllInvestmentsWithInterest(array $allInvestments): float
    {
        $filteredInvestments = array_filter($allInvestments, function(Investment $investment) {
            return $investment->getTotalInterestReceived() > 0;
        });

        if (empty($filteredInvestments)) {
            return 0.0;
        }

        $totalCapital = 0;
        $totalInterest = 0;
        $totalMonths = 0;
        $now = new DateTimeImmutable();

        foreach ($filteredInvestments as $investment) {
            $totalCapital += $investment->getStartingCapital();
            $totalInterest += $investment->getTotalInterestReceived();
            
            // On calcule la différence en mois entre la date de début et la date actuelle
            $diff = $investment->getStartAt()->diff($now);
            $months = ($diff->y * 12) + $diff->m;
            $totalMonths += max(1, $months); // S'assurer qu'on ne divise pas par zéro
        }

        // Calcule la moyenne des mois écoulés
        $averageMonths = $totalMonths / count($filteredInvestments);
        
        // Calcule le rendement total
        $totalReturn = ($totalCapital > 0) ? $totalInterest / $totalCapital : 0;

        // Calcule le rendement annualisé en le projetant sur 12 mois
        return ($totalReturn / $averageMonths) * 100 * 12;
    }

    public function calculateAverageRateFromAllInvestmentsWithInterest(array $allInvestments): float
    {
        $filteredInvestments = array_filter($allInvestments, function(Investment $investment) {
            return $investment->getTotalInterestReceived() > 0;
        });

        if (empty($filteredInvestments)) {
            return 0.0;
        }

        $sumOfRates = 0;
        $numberOfInvestments = count($filteredInvestments);

        foreach ($filteredInvestments as $investment) {
            $sumOfRates += $investment->getRate();
        }

        return ($sumOfRates / $numberOfInvestments);
    }

}