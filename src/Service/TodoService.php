<?php

namespace App\Service;

use App\Entity\Todo;
use App\Entity\TodoList;
use App\Repository\TodoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TodoService
{
    private TodoRepository $todoRepository;

    private PaginatorInterface $paginator;

    public function __construct(TodoRepository $todoRepository, PaginatorInterface $paginator)
    {
        $this->todoRepository = $todoRepository;
        $this->paginator = $paginator;
    }

    public function save(Todo $todo): void
    {
        $this->todoRepository->save($todo);
    }

    public function delete(Todo $todo): void
    {
        $this->todoRepository->delete($todo);
    }

    public function getPaginatedList(int $page, array $filters = [], UserInterface $user = null): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->todoRepository->queryAll($filters, $user),
            $page,
            TodoRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    public function findOneById(int $id): Todo
    {
        return $this->todoRepository->find($id);
    }
}