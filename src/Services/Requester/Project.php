<?php

namespace App\Services\Requester;

class Project extends Api
{
    const ALL_PROJECTS_ENDPOINT = 'projects/';

    public function getAll()
    {
        $response = $this->makeRequest(self::ALL_PROJECTS_ENDPOINT);

        return $response['data'];
    }

}