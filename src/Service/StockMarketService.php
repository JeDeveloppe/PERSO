<?php

namespace App\Service;

use App\Entity\StockMarket;

class StockMarketService{

    public function culculateGainOrLoss(StockMarket $stockMarket, array $priceData): float | null
    {
        $qte = $stockMarket->getQuantity();
        $buyPrice = $stockMarket->getPurchasePriceIncludingTVA() / 100;

        if($priceData['05. price'] == null){
            
            return null;

        }else{

            $actualPrice = $priceData['05. price'];
            $gainOrLoss = ($actualPrice - $buyPrice)  * $qte;
    
    
            return $gainOrLoss;
        }
    }

    public function calculateTotalValue(StockMarket $stockMarket, array $priceData): float | null
    {
        $qte = $stockMarket->getQuantity();
        $buyPrice = $stockMarket->getPurchasePriceIncludingTVA() / 100;

        if($priceData['05. price'] == null){
            
            return null;

        }else{

            $actualPrice = $priceData['05. price'];
            $totalValue = $actualPrice * $qte;
    
            return $totalValue;
        }
    }
}