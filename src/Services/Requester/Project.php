<?php

namespace App\Services\Requester;

use \App\Entity\Project as ProjectEntity;

class Project extends Api
{
    const PROJECTS_ENDPOINT = 'projects/';

    public function getAll()
    {
        $response = $this->makeRequest(self::PROJECTS_ENDPOINT);

        return $response['data'];
    }

    /**
     * @param ProjectEntity $project
     * @return array
     */
    public function createProject(ProjectEntity $project)
    {
        $data = [
            'title' => $project->getTitle(),
            'description' => $project->getDescription(),
            'client' => $project->getClient(),
            'company' => $project->getCompany(),
        ];

        return $this->makeRequest(self::PROJECTS_ENDPOINT, 'POST', $data);
    }

    /**
     * @param ProjectEntity $project
     * @return array
     */
    public function updateProject(ProjectEntity $project)
    {
        $data = [
            'title' => $project->getTitle(),
            'description' => $project->getDescription(),
            'client' => $project->getClient(),
            'company' => $project->getCompany(),
        ];

        return $this->makeRequest(sprintf('%s%s', self::PROJECTS_ENDPOINT, $project->getId()), 'PUT', $data);
    }

    /**
     * @param int $id
     * @return array
     */
    public function findProject(int $id)
    {
        $response = $this->makeRequest(sprintf('%s%s', self::PROJECTS_ENDPOINT, $id));
        return $response['data'];
    }

    /**
     * @param int $id
     * @return array
     */
    public function deleteProject(int $id)
    {
        return $this->makeRequest(sprintf('%s%s', self::PROJECTS_ENDPOINT, $id), 'DELETE');
    }


}