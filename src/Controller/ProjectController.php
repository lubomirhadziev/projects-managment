<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Services\Requester\Project as ProjectRequester;
use App\Entity\Project;
use App\Services\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var Project
     */
    private $projectRequester;

    private $serializer;

    /**
     * @param ProjectRepository $projectRepository
     * @param ProjectRequester $projectRequester
     */
    public function __construct(ProjectRepository $projectRepository, ProjectRequester $projectRequester, Serializer $serializer)
    {
        $this->projectRepository = $projectRepository;
        $this->projectRequester = $projectRequester;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="list_projects")
     */
    public function index()
    {
        $projects = $this->serializer->deserializeModel(
            $this->projectRequester->getAll(),
            Project::class
        );

        return $this->render('projects/list.html.twig', [
            'projects' => $projects
        ]);
    }

}