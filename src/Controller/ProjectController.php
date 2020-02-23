<?php

namespace App\Controller;

use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Services\ApiResponse;
use App\Services\Requester\Project as ProjectRequester;
use App\Entity\Project;
use App\Services\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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
     * @param Serializer $serializer
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
        $projects = $this->serializer->deserializeMultipleModel(
            $this->projectRequester->getAll(),
            Project::class
        );

        return $this->render('projects/list.html.twig', [
            'projects' => $projects
        ]);
    }

    /**
     * @Route("/project/new", name="create_project")
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createProject(Request $request)
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $errors = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newProject = $form->getData();

            $response = $this->projectRequester->createProject($newProject);
            $errors = $response['validation_errors'];

            if ($response['code'] == ApiResponse::SUCCESS_CODE) {
                return $this->redirectToRoute('list_projects');
            }
        }

        return $this->render('projects/new.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/project/delete/{id}", name="delete_project")
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteProject(int $id)
    {
        $this->projectRequester->deleteProject($id);

        return $this->redirectToRoute('list_projects');
    }

}