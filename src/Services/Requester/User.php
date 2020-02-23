<?php

namespace App\Services\Requester;

class User extends Api
{
    const USER_ENDPOINT = 'user/';

    /**
     * @param string $email
     * @param string $password
     * @return array
     */
    public function create(string $email, string $password)
    {
        $data = [
            'email' => $email,
            'password' => $password,
        ];

        return $this->makeRequest(self::USER_ENDPOINT, 'POST', $data);
    }

    public function validate(string $email, string $password)
    {
        $data = [
            'email' => $email,
            'password' => $password,
        ];

        return $this->makeRequest(sprintf('%s%s', self::USER_ENDPOINT, 'validate'), 'POST', $data);
    }

}