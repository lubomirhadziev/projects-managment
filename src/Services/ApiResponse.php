<?php

namespace App\Services;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    const SUCCESS_CODE = 0;
    const FAIL_CODE = -1;

    private $codes = [self::SUCCESS_CODE, self::FAIL_CODE];

    /**
     * Return json response with properly serialized model data
     * @param int $code
     * @param string $data
     * @param null|array $validationErrors
     * @return JsonResponse
     */
    public function model(int $code, string $data, $validationErrors = null): JsonResponse
    {
        return $this->response($code, json_decode($data), $validationErrors);
    }

    /**
     * @param int $code
     * @param null|array $data
     * @param null|array $validationErrors
     * @return JsonResponse
     */
    public function simple(int $code, $data = null, $validationErrors = null): JsonResponse
    {
        return $this->response($code, $data, $validationErrors);
    }

    /**
     * @param int $code
     * @param null|array|string $data
     * @param null|array $validationErrors
     * @return JsonResponse
     */
    private function response(int $code, $data = null, $validationErrors = null): JsonResponse
    {
        if (!in_array($code, $this->codes)) {
            throw new InvalidArgumentException(sprintf('Response code must be in [%s]', implode(',', $this->codes)));
        }

        $data = [
            'code' => $code,
            'data' => $data,
            'validation_errors' => $validationErrors
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

}