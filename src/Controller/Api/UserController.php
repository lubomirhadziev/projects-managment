<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Services\ApiResponse;
use App\Services\Serializer;
use App\Services\Utils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @package App\Controller\Api
 * @Route("/user", name="user_")
 */
class UserController extends ApiController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @param UserRepository $userRepository
     * @param Serializer $serializer
     * @param ApiResponse $apiResponse
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        UserRepository $userRepository,
        Serializer $serializer,
        ApiResponse $apiResponse,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        parent::__construct($serializer, $apiResponse);
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/validate", name="validate_user", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function validate(Request $request): JsonResponse
    {
        $errors = [];

        if (empty($request->get('email')) || empty($request->get('password'))) {
            $errors[] = 'Fill all required fields!';
        } else {
            $user = $this->userRepository->findOneBy(['email' => $request->get('email')]);

            if (!$user) {
                $errors[] = 'User not found';
            } elseif (!$this->passwordEncoder->isPasswordValid($user, $request->get('password'))) {
                $errors[] = 'Invalid password';
            }
        }

        return $this->apiResponse->model(
            (!empty($errors) ? ApiResponse::FAIL_CODE : ApiResponse::SUCCESS_CODE),
            $this->serializer->serializeModel($user),
            $errors
        );
    }

    /**
     * @Route("/", name="create_user", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param Utils $utils
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorInterface $validator, Utils $utils): JsonResponse
    {
        $user = new User();
        $user->setEmail($request->get('email'));
        $user->setPassword($this->passwordEncoder->encodePassword($user, $request->get('password')));

        $errors = $validator->validate($user);
        $createdUser = null;

        if (count($errors) > 0) {
            $errors = $utils->errorsToArray($errors);
        } else {
            $user->setApiToken($utils->generateToken());
            $createdUser = $this->userRepository->saveUser($user);
        }

        return $this->apiResponse->model(
            (!empty($errors) ? ApiResponse::FAIL_CODE : ApiResponse::SUCCESS_CODE),
            $this->serializer->serializeModel($createdUser),
            $errors
        );
    }

}