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
            $entityInDatabase->setStartingCapital($investment['Capital']);
            $entityInDatabase->setDuration($investment['Durée']);
            $entityInDatabase->setRate($investment['Taux']);
            $entityInDatabase->setInterestByMonth($investment['Montant / mois']);
            $entityInDatabase->setPaymentDate(5);

            $this->entityManagerInterface->persist($entityInDatabase);
        }

        $this->entityManagerInterface->flush();
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
