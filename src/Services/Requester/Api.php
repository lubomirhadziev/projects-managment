<?php

namespace App\Services\Requester;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class Api
{
    const API_ENDPOINT = 'http://127.0.0.1:8001/api/';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;

    }

    protected function makeRequest(string $endpoint)
    {
        $response = $this->client->request(
            'GET',
            $this->uri($endpoint),
            [
                'Content-Type' => 'application/json'
            ]
        );

        return json_decode($response->getBody(), true);
    }

    private function uri(string $uri)
    {
        return sprintf('%s%s', self::API_ENDPOINT, $uri);
    }

}