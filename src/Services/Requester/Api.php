<?php

namespace App\Services\Requester;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

abstract class Api
{
    /**
     * @var string
     */
    private $apiEndpoint;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Request
     */
    protected $security;

    /**
     * @param string $apiEndpoint
     * @param Client $client
     * @param Security $security
     */
    public function __construct(string $apiEndpoint, Client $client, Security $security)
    {
        $this->client = $client;
        $this->security = $security;
        $this->apiEndpoint = $apiEndpoint;
    }

    /**
     * @param string $endpoint
     * @param string $type
     * @param array $data
     * @return array
     */
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

    /**
     * @param string $uri
     * @return string
     */
    private function uri(string $uri)
    {
        return sprintf('%s%s', $this->apiEndpoint, $uri);
    }

}