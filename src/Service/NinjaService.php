<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\TransportException;

class NinjaService
{
    private const API_URL = 'https://api.api-ninjas.com/v1/stockprice';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $ninjaApiKey,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Récupère le prix actuel d'une action.
     *
     * @param string $symbol Le symbole de l'action (ex: 'ORA.PA').
     * @return array|null Les données de l'action ou null en cas d'erreur.
     */
    public function getStockPrice(string $symbol): ?array
    {
        if (empty($this->ninjaApiKey)) {
            $this->logger->error('Ninja API key is not set.');
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', self::API_URL, [
                'query' => [
                    'ticker' => $symbol,
                ],
                'headers' => [
                    'X-Api-Key' => $this->ninjaApiKey,
                ],
            ]);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                $this->logger->warning('Ninja API returned a non-OK status code.', ['status_code' => $response->getStatusCode()]);
                return null;
            }

            $content = $response->toArray();
            
            // L'API Ninja retourne un tableau vide si le symbole n'est pas trouvé
            if (empty($content)) {
                $this->logger->warning('Ninja API returned no data for the symbol.', ['symbol' => $symbol]);
                return null;
            }

            return $content;

        } catch (TransportException $e) {
            $this->logger->error('Transport error when calling Ninja API.', ['exception' => $e->getMessage()]);
            return null;
        }

        return null;
    }
}