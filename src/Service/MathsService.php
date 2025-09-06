<?php

namespace App\Service;


class MathsService {
    
    public function __construct(
    )
    {}

    public function transformeRateIntoPercentage(int $int): float
    {
        return $int / 100;
        
    }

}