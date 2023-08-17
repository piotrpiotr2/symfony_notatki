<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Service\TodoListService;
use App\Form\TodoListType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/todo-lists')]
class TodoListController extends AbstractController
{
    private TodoListService $todoListService;

    private TranslatorInterface $translator;

    public function __construct(TodoListService $todoListService, TranslatorInterface $translator)
    {
        $this->todoListService = $todoListService;
        $this->translator = $translator;
    }

    #[Route(
        name: 'todolist_index',
        methods: 'GET'
    )]
    public function index(): Response
    {
        $todoLists = $this->todoListService->getAll();
        return $this->render(
            'todo_lists/index.html.twig',
            ['todoLists' => $todoLists]
        );
    }

    #[Route(
        '/{id}',
        name: 'todolist_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(TodoList $todoList): Response
    {
        return $this->render('todo_list/show.html.twig', [ 'todoList' => $todoList ]);
    }

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
            $this->todoListService->save($todoList);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));
            return $this->redirectToRoute('todolist_index');
        }

        return $this->render('todolist/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(
        '/{id}/edit',
        name: 'todolist_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, TodoList $todoList): Response
    {
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
            'todo_list/edit.html.twig',
            ['form' => $form->createView(), 'todoList' => $todoList,]
        );
    }

    #[Route('/{id}/delete', name: 'todolist_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, TodoList $todoList): Response
    {
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
            'todo_list/delete.html.twig',
            ['form' => $form->createView(), 'todoList' => $todoList,]
        );
    }
}