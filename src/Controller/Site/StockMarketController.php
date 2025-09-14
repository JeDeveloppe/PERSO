<?php

namespace App\Controller\Site;

use App\Repository\StockMarketRepository;
use App\Service\AlphaVantageService;
use App\Service\StockMarketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StockMarketController extends AbstractController
{
    public function __construct(
        private StockMarketRepository $stockMarketRepository,
        private AlphaVantageService $alphaVantageService,
        private StockMarketService $stockMarketService
    )
    {
    }

    #[Route('/stock_market/wallet', name: 'app_stock_market_wallet')]
    public function wallet(): Response
    {
        // 1. Récupérer toutes les actions depuis la base de données
        // Assurez-vous que le nom de la classe est correct, par exemple 'Stock'
        $stocks = $this->stockMarketRepository->findAll();

        $stocksWithPrices = [];

        // 2. Parcourir chaque action pour obtenir son prix via l'API
        foreach($stocks as $stock) {
            // Supposons que votre entité Stock a une méthode getSymbol() qui retourne le code ISIN ou le ticker
            $symbol = $stock->getSymbol();
            $priceData = $this->alphaVantageService->getStockPrice($symbol);

            if (!$priceData) {
                $priceData['05. price'] = null;
            }

            $gainOrLoss = $this->stockMarketService->culculateGainOrLoss($stock, $priceData);
            $totalValue = $this->stockMarketService->calculateTotalValue($stock, $priceData);

            // 3. Ajouter les données de l'API à votre objet ou un tableau
            // Cela permet d'avoir toutes les informations nécessaires dans un seul objet
            $stocksWithPrices[] = [
                'entity' => $stock, // L'entité complète depuis la BDD
                'prices' => $priceData, // Les données de prix de l'API
                'gainOrLoss' => $gainOrLoss,
                'totalValue' => $totalValue
            ];
        }

        // 4. Transférer les données complètes au template
        return $this->render('site/stock_market/wallet.html.twig', [
            'stocks_with_prices' => $stocksWithPrices,
        ]);
    }
}