<?php
/**
 * TodoList Controller.
 */

namespace App\Controller;

use App\Entity\TodoList;
use App\Service\TodoListService;
use App\Form\TodoListType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TodoListController.
 */
#[Route('/todo-lists')]
class TodoListController extends AbstractController
{
    /**
     * Todo list service.
     */
    private TodoListService $todoListService;

    /**
     * Translator interface.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param TodoListService     $todoListService
     * @param TranslatorInterface $translator
     */
    public function __construct(TodoListService $todoListService, TranslatorInterface $translator)
    {
        $this->todoListService = $todoListService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @return Response
     */
    #[Route(
        name: 'todolist_index',
        methods: 'GET'
    )]
    public function index(): Response
    {
        $user = $this->getUser();
        $todoLists = $this->todoListService->getAll($user);

        return $this->render(
            'todo_lists/index.html.twig',
            ['todoLists' => $todoLists]
        );
    }

    /**
     * Show action.
     *
     * @param TodoList $todoList
     *
     * @return Response
     */
    #[Route(
        '/{id}',
        name: 'todolist_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(TodoList $todoList): Response
    {
        if ($todoList->getAuthor()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));

            return $this->redirectToRoute('note_index');
        }

        return $this->render('todo_lists/show.html.twig', ['todoList' => $todoList]);
    }

    /**
     * Create action.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[Route(
        '/create',
        name: 'todolist_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $todoList = new TodoList();
        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $todoList->setAuthor($this->getUser());
            $this->todoListService->save($todoList);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('todolist_index');
        }

        return $this->render('todo_lists/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action
     *
     * @param Request  $request
     * @param TodoList $todoList
     *
     * @return Response
     */
    #[Route(
        '/{id}/edit',
        name: 'todolist_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, TodoList $todoList): Response
    {
        if ($todoList->getAuthor()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));

            return $this->redirectToRoute('note_index');
        }
        $form = $this->createForm(
            TodoListType::class,
            $todoList,
            ['method' => 'PUT', 'action' => $this->generateUrl('todolist_edit', ['id' => $todoList->getId()])]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->todoListService->save($todoList);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('todolist_index');
        }

        return $this->render(
            'todo_lists/edit.html.twig',
            ['form' => $form->createView(), 'todoList' => $todoList]
        );
    }

    /**
     * Delete action.
     *
     * @param Request  $request
     * @param TodoList $todoList
     *
     * @return Response
     */
    #[Route('/{id}/delete', name: 'todolist_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, TodoList $todoList): Response
    {
        if ($todoList->getAuthor()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));

            return $this->redirectToRoute('note_index');
        }
        $form = $this->createForm(FormType::class, $todoList, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('todolist_delete', ['id' => $todoList->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->todoListService->delete($todoList);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('todolist_index');
        }

        return $this->render(
            'todo_lists/delete.html.twig',
            ['form' => $form->createView(), 'todoList' => $todoList]
        );
    }
}
