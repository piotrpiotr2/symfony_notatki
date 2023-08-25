<?php
/**
 * UserRegister Controller.
 */

namespace App\Controller;

use App\Entity\Enum\UserRole;
use App\Form\UserRegisterType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Security\LoginFormAuthenticator;

/**
 * Class UserRegisterController.
 */
class UserRegisterController extends AbstractController
{
    /**
     * User service.
     */
    private UserService $service;

    /**
     * User password hasher interface.
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * User authenticator interface.
     */
    private UserAuthenticatorInterface $userAuthenticator;

    /**
     * Login form authenticator interface.
     */
    private LoginFormAuthenticator $authenticator;

    /**
     * Constructor.
     *
     * @param UserService                 $service
     * @param UserPasswordHasherInterface $passwordHasher
     * @param UserAuthenticatorInterface  $userAuthenticator
     * @param LoginFormAuthenticator      $authenticator
     */
    public function __construct(UserService $service, UserPasswordHasherInterface $passwordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator)
    {
        $this->service = $service;
        $this->passwordHasher = $passwordHasher;
        $this->userAuthenticator = $userAuthenticator;
        $this->authenticator = $authenticator;
    }

    /**
     * Register.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[Route(
        '/register',
        name: 'app_register',
        methods: 'GET|POST'
    )]
    public function register(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('note_index');
        }

        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $user->setRoles([UserRole::ROLE_USER->value]);
            $this->service->save($user);
            $this->addFlash('success', 'registered');

            return $this->userAuthenticator->authenticateUser($user, $this->authenticator, $request);
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
