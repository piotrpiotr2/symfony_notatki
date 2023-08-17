<?php

namespace App\Service;

use App\Entity\TodoList;
use App\Repository\TodoListRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TodoListService
{
    /**
     * Article repository.
     */
    private TodoListRepository $todoListRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    public function __construct(TodoListRepository $todoListRepository, PaginatorInterface $paginator)
    {
        $this->todoListRepository = $todoListRepository;
        $this->paginator = $paginator;
    }

    public function save(TodoList $todoList): void
    {
        $this->todoListRepository->save($todoList);
    }

    public function delete(TodoList $todoList): void
    {
        $this->todoListRepository->delete($todoList);
    }

    public function getPaginatedList(int $page, array $filters = [], UserInterface $user = null): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->todoListRepository->queryAll($filters, $user),
            $page,
            TodoListRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    public function findOneById(int $id): TodoList
    {
        return $this->todoListRepository->find($id);
    }

    public function getAll(): array
    {
        return $this->todoListRepository->findAll();
    }
}