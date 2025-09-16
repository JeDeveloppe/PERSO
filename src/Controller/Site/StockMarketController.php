<?php

namespace App\Controller\Site;

use App\Repository\StockMarketRepository;
use App\Service\NinjaService;
use App\Service\StockMarketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class StockMarketController extends AbstractController
{
    public function __construct(
        private StockMarketRepository $stockMarketRepository,
        private NinjaService $ninjaService,
        private StockMarketService $stockMarketService
    )
    {
    }

    #[Route('/stock_market/wallet', name: 'app_stock_market_wallet')]
    public function wallet(): Response
    {
        $stocks = $this->stockMarketRepository->findAll();

        return $this->render('site/stock_market/wallet.html.twig', [
            'stocks' => $stocks,
        ]);
    }

    #[Route('/stock_market/get_prices', name: 'app_stock_market_get_prices', methods: ['GET'])]
    public function getPrices(): JsonResponse
    {
        $stocks = $this->stockMarketRepository->findAll();
        $stocksWithPrices = [];
        $apiDataCache = [];
        $totalValueOfTheWallet = 0;
        $gainOrLossTotalValue = 0;

        foreach($stocks as $stock) {
            $symbol = $stock->getSymbol();

            if (!isset($apiDataCache[$symbol])) {
                $datas = $this->ninjaService->getStockPrice($symbol);

                if ($datas != null && isset($datas['price'])) {
                    $actualPrice = $datas['price'];
                    $apiDataCache[$symbol] = $actualPrice;
                } else {
                    $actualPrice = null;
                }
            } else {
                $actualPrice = $apiDataCache[$symbol];
            }

            $gainOrLoss = $this->stockMarketService->culculateGainOrLoss($stock, $actualPrice);
            $gainOrLossTotalValue += $gainOrLoss;

            $totalValue = $this->stockMarketService->calculateTotalValue($stock, $actualPrice);
            $totalValueOfTheWallet += $totalValue;

            $stocksWithPrices[] = [
                'id' => $stock->getId(),
                'actualPrice' => $actualPrice,
                'gainOrLoss' => $gainOrLoss,
                'totalValue' => $totalValue
            ];
        }

        return new JsonResponse([
            'stocks' => $stocksWithPrices,
            'total_value_of_the_wallet' => $totalValueOfTheWallet,
            'gain_or_loss_total_value' => $gainOrLossTotalValue,
        ]);
    }
}