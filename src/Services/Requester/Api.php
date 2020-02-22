<?php

namespace App\Services\Requester;

use GuzzleHttp\Client;

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

    protected function makeRequest(string $endpoint, string $type = 'GET', array $data = [])
    {
        $response = $this->client->request(
            $type,
            $this->uri($endpoint),
            [
                'Content-Type' => 'application/json',
                'json' => $data
            ]
        );

        return json_decode($response->getBody(), true);
    }

    private function uri(string $uri)
    {
        return sprintf('%s%s', self::API_ENDPOINT, $uri);
    }

}