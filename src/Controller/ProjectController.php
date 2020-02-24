<?php

namespace App\Controller;

use App\Dto\ProjectDto;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Services\ApiResponse;
use App\Services\Requester\Project as ProjectRequester;
use App\Entity\Project;
use App\Services\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
            ProjectDto::class
        );

        return $this->render('projects/list.html.twig', [
            'projects' => $projects
        ]);
    }

    /**
     * @Route("/project/new", name="create_project")
     * @param Request $request
     * @return RedirectResponse|Response
     * @IsGranted("ROLE_USER")
     */
    public function createProject(Request $request)
    {
        $project = new Project();

        return $this->renderForm('projects/new.html.twig', $project, $request);
    }

    /**
     * @Route("/project/edit/{id}", name="edit_project")
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     * @IsGranted("ROLE_USER")
     */
    public function editProject(int $id, Request $request)
    {
        $project = $this->serializer->deserializeModel(
            $this->projectRequester->findProject($id),
            Project::class
        );

        return $this->renderForm('projects/edit.html.twig', $project, $request, true);
    }

    /**
     * @Route("/project/delete/{id}", name="delete_project")
     * @param int $id
     * @return RedirectResponse
     * @IsGranted("ROLE_USER")
     */
    public function deleteProject(int $id)
    {
        $this->projectRequester->deleteProject($id);

        return $this->redirectToRoute('list_projects');
    }

    /**
     * @param string $template
     * @param bool $isUpdate
     * @param Project $project
     * @param Request $request
     * @return RedirectResponse|Response
     */
    private function renderForm(string $template, Project $project, Request $request, bool $isUpdate = false)
    {
        $form = $this->createForm(ProjectType::class, $project);
        $errors = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newProject = $form->getData();

            if ($isUpdate) {
                $response = $this->projectRequester->updateProject($newProject);
            } else {
                $response = $this->projectRequester->createProject($newProject);
            }
            $errors = $response['validation_errors'];

            if (empty($errors)) {
                return $this->redirectToRoute('list_projects');
            }
        }

        return $this->render($template, [
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

}