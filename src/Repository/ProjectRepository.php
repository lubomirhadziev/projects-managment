<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
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
        parent::__construct($registry, Project::class);
        $this->manager = $manager;
    }

    /**
     * @param Project $project
     * @return Project
     */
    public function saveProject(Project $project): Project
    {
        $this->manager->persist($project);
        $this->manager->flush();

        return $project;
    }
}
