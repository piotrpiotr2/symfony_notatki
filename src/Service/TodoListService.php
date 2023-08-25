<?php
/**
 * TodoList service
 */

namespace App\Service;

use App\Entity\TodoList;
use App\Repository\TodoListRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TodoListService
 */
class TodoListService
{
    /**
     * Todo list repository.
     */
    private TodoListRepository $todoListRepository;

    /**
     * Constructor
     *
     * @param TodoListRepository $todoListRepository
     */
    public function __construct(TodoListRepository $todoListRepository)
    {
        $this->todoListRepository = $todoListRepository;
    }

    /**
     * Save
     *
     * @param TodoList $todoList
     * @return void
     */
    public function save(TodoList $todoList): void
    {
        $this->todoListRepository->save($todoList);
    }

    /**
     * Delete
     *
     * @param TodoList $todoList
     * @return void
     */
    public function delete(TodoList $todoList): void
    {
        $this->todoListRepository->delete($todoList);
    }

    /**
     * Get all
     *
     * @param UserInterface $user
     * @return array
     */
    public function getAll(UserInterface $user): array
    {
        return $this->todoListRepository->queryByAuthor($user)->getQuery()->execute();
    }
}