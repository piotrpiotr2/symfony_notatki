<?php
/**
 * Todo Controller
 */

namespace App\Controller;

use App\Entity\Todo;
use App\Entity\TodoList;
use App\Form\TodoType;
use App\Service\TodoService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TodoController
 */
#[Route('/todos')]
class TodoController extends AbstractController
{
    /**
     * Todo service
     *
     * @var TodoService
     */
    private TodoService $todoService;

    /**
     * Translator interface
     *
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * Constructor
     *
     * @param TodoService $todoService
     * @param TranslatorInterface $translator
     */
    public function __construct(TodoService $todoService, TranslatorInterface $translator)
    {
        $this->todoService = $todoService;
        $this->translator = $translator;
    }

    /**
     * Create action
     *
     * @param Request $request
     * @param TodoList $todoList
     * @return Response
     */
    #[Route(
        '/create/{id}',
        name: 'todo_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request, TodoList $todoList): Response
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $todo->setTodoList($todoList);
            $this->todoService->save($todo);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));
            return $this->redirectToRoute('todolist_index');
        }

        return $this->render('todo/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action
     *
     * @param Request $request
     * @param Todo $todo
     * @return Response
     */
    #[Route(
        '/{id}/edit',
        name: 'todo_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Todo $todo): Response
    {
        $form = $this->createForm(
            TodoType::class,
            $todo,
            ['method' => 'PUT', 'action' => $this->generateUrl('todo_edit', ['id' => $todo->getId()])]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->todoService->save($todo);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('todolist_index');
        }

        return $this->render(
            'todo/edit.html.twig',
            ['form' => $form->createView(), 'todo' => $todo,]
        );
    }
}