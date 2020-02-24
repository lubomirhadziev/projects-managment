<?php

namespace App\Twig;

use App\Entity\Task;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TaskStatusExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('taskStatus', [$this, 'taskStatus']),
        ];
    }

    /**
     * @param int $index
     * @return string
     */
    public function taskStatus(int $index): string
    {
        if (isset(Task::STATUS_NAME_MAPPING[$index])) {
            return Task::STATUS_NAME_MAPPING[$index];
        }

        return null;
    }

}