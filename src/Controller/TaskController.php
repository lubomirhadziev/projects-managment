<?php

namespace App\Controller;

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
        $tasks = $this->serializer->deserializeMultipleModel(
            $this->taskRequester->getAll($projectId),
            Task::class
        );

        return $this->render('tasks/list.html.twig', [
            'tasks' => $tasks,
            'projectId' => $projectId
        ]);
    }

    /**
     * @Route("/task/new/{projectId}", name="create_task")
     * @param int $projectId
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createTask(int $projectId, Request $request)
    {
        $project = $this->serializer->deserializeModel(
            $this->projectRequester->findProject($projectId),
            Project::class
        );

        $task = new Task();
        $task->setProject($project);
        $form = $this->createForm(TaskType::class, $task);
        $errors = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newTask = $form->getData();

            $response = $this->taskRequester->createTask($newTask);
            $errors = $response['validation_errors'];

            if ($response['code'] == ApiResponse::SUCCESS_CODE) {
                return $this->redirectToRoute('list_tasks', ['projectId' => $projectId]);
            }
        }

        return $this->render('tasks/new.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/task/delete/{projectId}/{taskId}", name="delete_task")
     * @param int $projectId
     * @param int $taskId
     * @return RedirectResponse
     */
    public function deleteProject(int $projectId, int $taskId)
    {
        $this->taskRequester->deleteTask($taskId);

        return $this->redirectToRoute('list_tasks', ['projectId' => $projectId]);
    }

}