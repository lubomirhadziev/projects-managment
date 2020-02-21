<?php

namespace App\Controller\Api;

use App\Services\ApiResponse;
use App\Services\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var ApiResponse
     */
    protected $apiResponse;

    public function __construct(Serializer $serializer, ApiResponse $apiResponse)
    {
        $this->serializer = $serializer;
        $this->apiResponse = $apiResponse;
    }

}