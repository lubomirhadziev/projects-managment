<?php

namespace App\Services\Requester;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

abstract class Api
{
    const API_ENDPOINT = 'http://127.0.0.1:8001/api/';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Request
     */
    protected $security;

    /**
     * @param Client $client
     * @param Security $security
     */
    public function __construct(Client $client, Security $security)
    {
        $this->client = $client;
        $this->security = $security;
    }

    protected function makeRequest(string $endpoint, string $type = 'GET', array $data = [])
    {
        $headers = [];

        if ($this->security->getUser()) {
            $headers['X-AUTH-TOKEN'] = $this->security->getUser()->getApiToken();
        }

        $response = $this->client->request(
            $type,
            $this->uri($endpoint),
            [
                'Content-Type' => 'application/json',
                'json' => $data,
                'headers' => $headers
            ]
        );

        return json_decode($response->getBody(), true);
    }

    private function uri(string $uri)
    {
        return sprintf('%s%s', self::API_ENDPOINT, $uri);
    }

}