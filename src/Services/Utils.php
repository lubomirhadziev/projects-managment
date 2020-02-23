<?php

namespace App\Services;

use Symfony\Component\Validator\ConstraintViolationList;

class Utils
{
    public function generateToken($length = 30): string
    {
        return rtrim(strtr(base64_encode(random_bytes($length)), '+/', '-_'), '=');
    }

    /**
     * @param ConstraintViolationList $errors
     * @return array
     */
    public function errorsToArray(ConstraintViolationList $errors): array
    {
        $newErrors = [];

        foreach ($errors as $error) {
            $newErrors[] = sprintf('%s: %s', ucfirst($error->getPropertyPath()), $error->getMessage());
        }

        return $newErrors;
    }

}