<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Services\ApiResponse;
use App\Services\Serializer;
use App\Services\Utils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @package App\Controller\Api
 * @Route("/tasks", name="tasks_")
 */
class TaskController extends ApiController
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @param TaskRepository $taskRepository
     * @param ProjectRepository $projectRepository
     * @param Serializer $serializer
     * @param ApiResponse $apiResponse
     */
    public function __construct(
        TaskRepository $taskRepository,
        ProjectRepository $projectRepository,
        Serializer $serializer,
        ApiResponse $apiResponse
    )
    {
        parent::__construct($serializer, $apiResponse);
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Route("/", name="create_task", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param Utils $utils
     * @return JsonResponse
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request, ValidatorInterface $validator, Utils $utils): JsonResponse
    {
        $task = $this->serializer->deserializeModel($request->getContent(), Task::class);

        if ($request->get('project_id') != null) {
            $project = $this->projectRepository->findOneBy(['id' => $request->request->get('project_id')]);
            $task->setProject($project);
        }

        return $this->validateAndSave($task, $validator, $utils);
    }

    /**
     * @Route("/{id}", name="update_task", methods={"PUT"})
     * @param int $id
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param Utils $utils
     * @return JsonResponse
     * @IsGranted("ROLE_USER")
     */
    public function update(int $id, Request $request, ValidatorInterface $validator, Utils $utils): JsonResponse
    {
        $existingTask = $this->taskRepository->findOneBy(['id' => $id]);

        $task = $this->serializer->deserializeModel($request->getContent(), Task::class, $existingTask);

        if ($request->get('project_id') != null) {
            $project = $this->projectRepository->findOneBy(['id' => $request->request->get('project_id')]);
            $task->setProject($project);
        }

        return $this->validateAndSave($task, $validator, $utils);
    }

    /**
     * @Route("/find/{id}", name="find_task_by_id", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function find(int $id): JsonResponse
    {
        $task = $this->taskRepository->findOneBy(['id' => $id]);

        return $this->apiResponse->model(
            ApiResponse::SUCCESS_CODE,
            $this->serializer->serializeModel($task)
        );
    }

    /**
     * @Route("/{projectId}", name="get_all_tasks", methods={"GET"})
     * @param int $projectId
     * @return JsonResponse
     */
    public function getAll(int $projectId): JsonResponse
    {
        $project = $this->projectRepository->find($projectId);
        $tasks = $this->taskRepository->getTasksByProject($project);

        return $this->apiResponse->model(
            ApiResponse::SUCCESS_CODE,
            $this->serializer->serializeModel($tasks)
        );
    }

    /**
     * @Route("/{id}", name="delete_task_by_id", methods={"DELETE"})
     * @param int $id
     * @return JsonResponse
     * @IsGranted("ROLE_USER")
     */
    public function delete(int $id): JsonResponse
    {
        $task = $this->taskRepository->findOneBy(['id' => $id]);

        if (!$task) {
            return $this->apiResponse->simple(ApiResponse::FAIL_CODE);
        }

        $this->taskRepository->removeTask($task);

        return $this->apiResponse->simple(ApiResponse::SUCCESS_CODE);
    }


    /**
     * @param Task $task
     * @param ValidatorInterface $validator
     * @param Utils $utils
     * @return JsonResponse
     */
    private function validateAndSave(Task $task, ValidatorInterface $validator, Utils $utils)
    {
        $errors = $validator->validate($task);
        $validationErrors = [];

        if (count($errors) > 0) {
            $validationErrors = $utils->errorsToArray($errors);
        } else {
            $task = $this->taskRepository->saveTask($task);
        }

        return $this->apiResponse->model(
            (!empty($validationErrors) ? ApiResponse::FAIL_CODE : ApiResponse::SUCCESS_CODE),
            $this->serializer->serializeModel($task),
            $validationErrors
        );
    }

}