<?php
/**
 * User controller
 */

namespace App\Controller;

use App\Entity\Enum\UserRole;
use App\Form\EditUserPasswordType;
use App\Form\UserType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class UserController
 */
#[Route('/users')]
class UserController extends AbstractController
{
    /**
     * Translator interface
     *
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * User service
     *
     * @var UserService
     */
    private UserService $userService;

    /**
     * Constructor
     *
     * @param UserService $userService
     * @param TranslatorInterface $translator
     */
    public function __construct(UserService $userService, TranslatorInterface $translator)
    {
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * Index action
     *
     * @return Response
     */
    #[Route(
        name: 'user_index',
        methods: 'GET'
    )]
    public function index(): Response
    {
        if (!in_array(UserRole::ROLE_ADMIN->value, $this->getUser()->getRoles())) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));
            return $this->redirectToRoute('note_index');
        }

        $users = $this->userService->findAll();
        return $this->render('user/index.html.twig', ['users' => $users]);
    }

    /**
     * Edit action
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    #[Route(
        '/{id}/edit',
        name: 'edit_user',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function editUser(Request $request, User $user): Response
    {
        if (!in_array(UserRole::ROLE_ADMIN->value, $this->getUser()->getRoles()) && !$this->getUser()->getUserIdentifier() == $user->getUserIdentifier()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));
            return $this->redirectToRoute('note_index');
        }

        $form = $this->createForm(UserType::class, $user, [
                'method' => 'PUT',
                'action' => $this->generateUrl('edit_user', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit_user.html.twig', [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Show action
     *
     * @param User $user
     * @return Response
     */
    #[Route(
        '/{id}',
        name: 'user_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    #[IsGranted(
        'VIEW',
        subject: 'user'
    )]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * Change password action
     *
     * @param Request $request
     * @param User $user
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    #[Route(
        '/{id}/change-password',
        name: 'user_change_password',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function changePassword(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!in_array(UserRole::ROLE_ADMIN->value, $this->getUser()->getRoles())
            && !$this->getUser()->getUserIdentifier() == $user->getUserIdentifier()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));
            return $this->redirectToRoute('note_index');
        }

        $form = $this->createForm(EditUserPasswordType::class, $user, ['method' => 'PUT']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
            $this->userService->save($user);
            $this->addFlash('success', 'message.updated_successfully');
            $this->redirectToRoute('note_index');
        }

        return $this->render('user/password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}