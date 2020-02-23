<?php

namespace App\Services\Requester;

use \App\Entity\Task as TaskEntity;

class Task extends Api
{
    const TASKS_ENDPOINT = 'tasks/';

    /**
     * @param int $projectId
     * @return array
     */
    public function getAll(int $projectId)
    {
        $response = $this->makeRequest(sprintf('%s%s', self::TASKS_ENDPOINT, $projectId));

        return $response['data'];
    }

    /**
     * @param TaskEntity $task
     * @return array
     */
    public function createTask(TaskEntity $task)
    {
        $data = [
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'duration' => $task->getDuration(),
            'project_id' => $task->getProject()->getId()
        ];

        return $this->makeRequest(self::TASKS_ENDPOINT, 'POST', $data);
    }

    /**
     * @param int $id
     * @return array
     */
    public function deleteTask(int $id)
    {
        return $this->makeRequest(sprintf('%s%s', self::TASKS_ENDPOINT, $id), 'DELETE');
    }

}