<?php

namespace App\Service;

use App\Entity\StockMarket;

class StockMarketService{

    public function culculateGainOrLoss(StockMarket $stockMarket, ?float $actualPrice): float | null
    {
        $qte = $stockMarket->getQuantity();
        $buyPrice = $stockMarket->getPurchasePriceIncludingTVA() / 100;

        if($actualPrice == null){
            
            return null;

        }else{

            $gainOrLoss = ($actualPrice - $buyPrice)  * $qte;
    
            return $gainOrLoss;
        }
    }

    public function calculateTotalValue(StockMarket $stockMarket, ?float $actualPrice): float | null
    {
        $qte = $stockMarket->getQuantity();

        if($actualPrice == null){
            
            return 0;

        }else{

            $totalValue = $actualPrice * $qte;
    
            return $totalValue;
        }
    }
}