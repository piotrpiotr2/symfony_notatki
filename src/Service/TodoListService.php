<?php

namespace App\Service;

use App\Entity\TodoList;
use App\Repository\TodoListRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class TodoListService
{
    /**
     * Todo list repository.
     */
    private TodoListRepository $todoListRepository;

    public function __construct(TodoListRepository $todoListRepository)
    {
        $this->todoListRepository = $todoListRepository;
    }

    public function save(TodoList $todoList): void
    {
        $this->todoListRepository->save($todoList);
    }

    public function delete(TodoList $todoList): void
    {
        $this->todoListRepository->delete($todoList);
    }

    public function getAll(UserInterface $user): array
    {
        return $this->todoListRepository->queryByAuthor($user)->getQuery()->execute();
    }
}