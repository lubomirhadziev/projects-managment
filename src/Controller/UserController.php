<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Security\TokenAuthenticator;
use App\Services\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \App\Services\Requester\User as UserRequester;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserController extends AbstractController
{
    private $userRequester;
    private $serializer;
    private $authenticator;
    private $guardHandler;

    /**
     * @param UserRequester $userRequester
     * @param Serializer $serializer
     * @param TokenAuthenticator $authenticator
     * @param GuardAuthenticatorHandler $guardHandler
     */
    public function __construct(
        UserRequester $userRequester,
        Serializer $serializer,
        TokenAuthenticator $authenticator,
        GuardAuthenticatorHandler $guardHandler)
    {
        $this->userRequester = $userRequester;
        $this->serializer = $serializer;
        $this->authenticator = $authenticator;
        $this->guardHandler = $guardHandler;
    }

    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $errors = [];

        if ($request->isMethod('POST')) {
            $response = $this->userRequester->validate($request->get('email'), $request->get('password'));
            $errors = $response['validation_errors'];

            if (!$errors) {
                return $this->authenticateUser(
                    $this->serializer->deserializeModel($response['data'], User::class),
                    $request
                );
            }
        }

        return $this->render('auth/login.html.twig', [
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param TokenAuthenticator $authenticator
     * @param GuardAuthenticatorHandler $guardHandler
     * @return Response
     */
    public function register(Request $request, TokenAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler)
    {
        $errors = [];

        if ($request->isMethod('POST')) {
            $response = $this->userRequester->create($request->get('email'), $request->get('password'));
            $errors = $response['validation_errors'];

            if (!$errors) {
                return $this->authenticateUser(
                    $this->serializer->deserializeModel($response['data'], User::class),
                    $request
                );
            }
        }

        return $this->render('auth/register.html.twig', ['errors' => $errors]);
    }

    /**
     * @Route("/logout", name="user_logout", methods={"GET"})
     */
    public function logout()
    {
    }

    /**
     * @param User $user
     * @param Request $request
     * @return RedirectResponse
     */
    private function authenticateUser(User $user, Request $request): RedirectResponse
    {
        $this->guardHandler->authenticateUserAndHandleSuccess($user, $request, $this->authenticator, 'main');

        return $this->redirectToRoute('list_projects');
    }
}