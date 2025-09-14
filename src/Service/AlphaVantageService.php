<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\TransportException;

class AlphaVantageService
{
    private const API_URL = 'https://www.alphavantage.co/query';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $alphaVantageApiKey,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Récupère le prix actuel et d'autres données pour une action.
     *
     * @param string $symbol Le symbole de l'action (ex: 'AAPL').
     * @return array|null Les données de l'action ou null en cas d'erreur.
     */
    public function getStockPrice(string $symbol): ?array
    {
        if (empty($this->alphaVantageApiKey)) {
            $this->logger->error('Alpha Vantage API key is not set.');
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', self::API_URL, [
                'query' => [
                    'function' => 'GLOBAL_QUOTE',
                    'symbol' => $symbol,
                    'apikey' => $this->alphaVantageApiKey,
                ],
            ]);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                $this->logger->warning('Alpha Vantage API returned a non-OK status code.', ['status_code' => $response->getStatusCode()]);
                return null;
            }

            $content = $response->toArray();
            
            // Gestion des messages d'erreur de l'API (limite de requêtes, symbole invalide, etc.)
            if (isset($content['Error Message'])) {
                $this->logger->error('Alpha Vantage API returned an error message.', ['message' => $content['Error Message']]);
                return null;
            }

            if (isset($content['Note'])) {
                $this->logger->warning('Alpha Vantage API call limit reached.', ['message' => $content['Note']]);
                return null;
            }

            if (isset($content['Global Quote']) && !empty($content['Global Quote'])) {
                return $content['Global Quote'];
            }

            $this->logger->warning('Alpha Vantage API response does not contain Global Quote data.', ['response' => $content]);

        } catch (TransportException $e) {
            $this->logger->error('Transport error when calling Alpha Vantage API.', ['exception' => $e->getMessage()]);
            return null;
        }

        return null;
    }
}