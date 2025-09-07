<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\Ceiling;

/**
 * Service pour calculer les intérêts futurs d'un compte en fonction des plafonds.
 */
class AccountService
{
    /**
     * Calcule le solde et les intérêts pour un nombre d'années futures.
     *
     * @param Account $account L'entité Compte sur laquelle effectuer le calcul.
     * @param int $years Le nombre d'années à calculer (par défaut, 5).
     * @return array Un tableau contenant les détails pour chaque année.
     */
    public function calculateFutureInterests(Account $account, int $years = 5): array
    {
        // On récupère les plafonds du compte et on les trie par valeur
        // pour s'assurer que le calcul est effectué dans le bon ordre.
        $ceilings = $account->getCeiling()->toArray();
        usort($ceilings, function (Ceiling $a, Ceiling $b) {
            return $a->getValue() <=> $b->getValue();
        });

        $results = [];
        $currentBalance = $account->getValue();

        for ($i = 1; $i <= $years; $i++) {
            $applicableCeiling = null;
            $rateToApply = 0.0;

            // On cherche le plafond applicable en fonction du solde actuel.
            foreach ($ceilings as $ceiling) {
                // Le plafond -100 signifie "l'infini"
                if ($ceiling->getValue() == -100 || $currentBalance <= $ceiling->getValue()) {
                    $applicableCeiling = $ceiling;
                    $rateToApply = $applicableCeiling->getRate();
                    break;
                }
            }

            if ($applicableCeiling) {
                // On convertit le taux de la chaîne de caractères en float.
                $rate = floatval($rateToApply) / 100;

                // On calcule les intérêts et le nouveau solde pour l'année.
                $interest = $currentBalance * $rate;
                $newBalance = $currentBalance + $interest;

                $results[] = [
                    'year' => date('Y') + $i,
                    'initial_balance' => round($currentBalance, 2),
                    'interest_rate' => $applicableCeiling->getRate(),
                    'interest_earned' => round($interest, 2),
                    'final_balance' => round($newBalance, 2),
                ];

                // Le solde de l'année suivante est le solde final de l'année en cours.
                $currentBalance = $newBalance;
            } else {
                // Si aucun plafond n'est trouvé, on arrête le calcul.
                break;
            }
        }

        return $results;
    }
}