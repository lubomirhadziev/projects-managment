<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Services\ApiResponse;
use App\Services\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @package App\Controller\Api
 * @Route("/projects", name="projects_")
 */
class ProjectController extends ApiController
{
    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @param ProjectRepository $taskRepository
     * @param Serializer $serializer
     */
    public function __construct(ProjectRepository $taskRepository, Serializer $serializer, ApiResponse $apiResponse)
    {
        parent::__construct($serializer, $apiResponse);
        $this->projectRepository = $taskRepository;
    }

    /**
     * @Route("/", name="create_project", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request): JsonResponse
    {
        $project = $this->serializer->deserializeModel($request->getContent(), Project::class);

        $this->projectRepository->saveProject($project);

        return $this->apiResponse->model(
            ApiResponse::SUCCESS_CODE,
            $this->serializer->serializeModel($project)
        );
    }

    /**
     * @Route("/{id}", name="update_project", methods={"PUT"})
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @IsGranted("ROLE_USER")
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $existingProject = $this->projectRepository->findOneBy(['id' => $id]);

        $project = $this->serializer->deserializeModel($request->getContent(), Project::class, $existingProject);
        $this->projectRepository->saveProject($project);

        return $this->apiResponse->model(ApiResponse::SUCCESS_CODE, $this->serializer->serializeModel($project));
    }

    /**
     * @Route("/{id}", name="find_project_by_id", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     */
    public function find(int $id): JsonResponse
    {
        $project = $this->projectRepository->findOneBy(['id' => $id]);

        return $this->apiResponse->model(
            ApiResponse::SUCCESS_CODE,
            $this->serializer->serializeModel($project)
        );
    }

    /**
     * @Route("/", name="get_all_projects", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $projects = $this->projectRepository->findAll();

        return $this->apiResponse->model(
            ApiResponse::SUCCESS_CODE,
            $this->serializer->serializeModel($projects)
        );
    }

    /**
     * @Route("/{id}", name="delete_project_by_id", methods={"DELETE"})
     * @param int $id
     * @IsGranted("ROLE_USER")
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $project = $this->projectRepository->findOneBy(['id' => $id]);

        if (!$project) {
            return $this->apiResponse->simple(ApiResponse::FAIL_CODE);
        }

        $this->projectRepository->removeProject($project);

        return $this->apiResponse->simple(ApiResponse::SUCCESS_CODE);
    }

}