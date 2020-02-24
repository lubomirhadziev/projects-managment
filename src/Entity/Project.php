<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $client;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="project")
     */
    private $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(?string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function tasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * Sum duration of all tasks in current project
     * @return int
     */
    public function getDuration()
    {
        $duration = 0;

        foreach ($this->tasks() as $task) {
            $duration += $task->getDuration();
        }

        return $duration;
    }

    /**
     * Detect status of the project
     * @return int
     */
    public function getStatus()
    {
        $status = Task::STATUS_NEW;
        $status = $this->applyPendingStatus($status);
        $status = $this->applyFailedStatus($status);
        $status = $this->applyDoneStatus($status);

        return $status;
    }

    /**
     * @param int $currentStatus
     * @return string
     */
    private function applyPendingStatus(int $currentStatus)
    {
        foreach ($this->tasks() as $task) {
            if (
                ($task->getStatus() == Task::STATUS_PENDING || $task->getStatus() > Task::STATUS_PENDING)
                && Task::STATUS_PENDING > $currentStatus
            ) {
                $currentStatus = Task::STATUS_PENDING;
                break;
            }
        }

        return $currentStatus;
    }

    /**
     * @param int $currentStatus
     * @return string
     */
    private function applyFailedStatus(int $currentStatus)
    {
        $allTasksFailed = true;

        foreach ($this->tasks() as $task) {
            if ($task->getStatus() != Task::STATUS_FAILED) {
                $allTasksFailed = false;
                break;
            }
        }

        if ($allTasksFailed && !empty($this->tasks())) {
            return Task::STATUS_FAILED;
        }

        return $currentStatus;
    }

    /**
     * @param int $currentStatus
     * @return string
     */
    private function applyDoneStatus(int $currentStatus)
    {
        $allTasksDone = true;

        foreach ($this->tasks() as $task) {
            if ($task->getStatus() != Task::STATUS_DONE) {
                $allTasksDone = false;
                break;
            }
        }

        if ($allTasksDone && !empty($this->tasks())) {
            return Task::STATUS_DONE;
        }

        return $currentStatus;
    }
}
