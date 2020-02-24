<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    )
    {
        parent::__construct($registry, Task::class);
        $this->manager = $manager;
    }

    public function getTasksByProject(Project $project)
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.project = :project')
            ->setParameter('project', $project)
            ->orderBy('t.id', 'desc');

        return $qb->getQuery()->execute();
    }

    /**
     * @param Task $task
     * @return Task
     */
    public function saveTask(Task $task): Task
    {
        $this->manager->persist($task);
        $this->manager->flush();

        return $task;
    }

    /**
     * @param Task $task
     */
    public function removeTask(Task $task)
    {
        $this->manager->remove($task);
        $this->manager->flush();
    }

}
