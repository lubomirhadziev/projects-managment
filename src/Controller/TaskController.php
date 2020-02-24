<?php

namespace App\Controller;

use App\Dto\TaskDto;
use App\Entity\Task;
use App\Form\TaskType;
use App\Services\ApiResponse;
use App\Services\Requester\Task as TaskRequester;
use App\Services\Requester\Project as ProjectRequester;
use App\Entity\Project;
use App\Services\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class TaskController extends AbstractController
{

    /**
     * @var Task
     */
    private $taskRequester;

    /**
     * @var ProjectRequester
     */
    private $projectRequester;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param TaskRequester $taskRequester
     * @param ProjectRequester $projectRequester
     * @param Serializer $serializer
     */
    public function __construct(
        TaskRequester $taskRequester,
        ProjectRequester $projectRequester,
        Serializer $serializer)
    {
        $this->taskRequester = $taskRequester;
        $this->projectRequester = $projectRequester;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/tasks/list/{projectId}", name="list_tasks")
     * @param int $projectId
     * @return Response
     */
    public function index(int $projectId)
    {
        $project = $this->serializer->deserializeModel(
            $this->projectRequester->findProject($projectId),
            Project::class
        );

        $tasks = $this->serializer->deserializeMultipleModel(
            $this->taskRequester->getAll($projectId),
            TaskDto::class
        );

        return $this->render('tasks/list.html.twig', [
            'tasks' => $tasks,
            'project' => $project
        ]);
    }

    /**
     * @Route("/task/new/{projectId}", name="create_task")
     * @param int $projectId
     * @param Request $request
     * @return RedirectResponse|Response
     * @IsGranted("ROLE_USER")
     */
    public function createTask(int $projectId, Request $request)
    {
        $project = $this->serializer->deserializeModel(
            $this->projectRequester->findProject($projectId),
            Project::class
        );

        $task = new Task();
        $task->setProject($project);

        return $this->renderForm('tasks/new.html.twig', $task, $project, $request);
    }

    /**
     * @Route("/task/edit/{id}", name="edit_task")
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     * @IsGranted("ROLE_USER")
     */
    public function editTask(int $id, Request $request)
    {
        $task = $this->serializer->deserializeModel(
            $this->taskRequester->findTask($id),
            Task::class
        );

        return $this->renderForm('tasks/edit.html.twig', $task, $task->getProject(), $request);
    }

    /**
     * @Route("/task/delete/{projectId}/{taskId}", name="delete_task")
     * @param int $projectId
     * @param int $taskId
     * @return RedirectResponse
     * @IsGranted("ROLE_USER")
     */
    public function deleteProject(int $projectId, int $taskId)
    {
        $this->taskRequester->deleteTask($taskId);

        return $this->redirectToRoute('list_tasks', ['projectId' => $projectId]);
    }

    /**
     * @param string $template
     * @param Task $task
     * @param Project $project
     * @param Request $request
     * @return RedirectResponse|Response
     */
    private function renderForm(string $template, Task $task, Project $project, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);
        $errors = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newTask = $form->getData();

            $response = $this->taskRequester->createTask($newTask);
            $errors = $response['validation_errors'];

            if ($response['code'] == ApiResponse::SUCCESS_CODE) {
                return $this->redirectToRoute('list_tasks', ['projectId' => $project->getId()]);
            }
        }

        return $this->render($template, [
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

}