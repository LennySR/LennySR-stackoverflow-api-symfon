<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class StackOverflowApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Llama a la API de Stack Overflow y devuelve las preguntas.
     *
     * @param string $tagged
     * @param int|null $fromDate Timestamp opcional para la fecha desde
     * @param int|null $toDate Timestamp opcional para la fecha hasta
     * @return array
     * @throws \Exception Si la API externa falla
     */
    public function fetchQuestions(string $tagged, ?int $fromDate = null, ?int $toDate = null): array
    {
        $url = 'https://api.stackexchange.com/2.3/questions';
        $queryParams = [
            'tagged' => $tagged,
            'fromdate' => $fromDate,
            'todate' => $toDate,
            'site' => 'stackoverflow'
        ];

        // Llamada a la API de Stack Overflow
        $response = $this->client->request('GET', $url, ['query' => $queryParams]);

        // Verificar si la API respondió correctamente
        if ($response->getStatusCode() !== 200) {
            throw new \Exception("Error al consultar la API de Stack Overflow");
        }

        return $response->toArray()['items'] ?? [];
    }
}