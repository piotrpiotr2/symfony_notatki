<?php
/**
 * Todo service
 */

namespace App\Service;

use App\Entity\Todo;
use App\Entity\TodoList;
use App\Repository\TodoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TodoService
 */
class TodoService
{
    /**
     * Todo repository
     *
     * @var TodoRepository
     */
    private TodoRepository $todoRepository;

    /**
     * Paginator interface
     *
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor
     *
     * @param TodoRepository $todoRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(TodoRepository $todoRepository, PaginatorInterface $paginator)
    {
        $this->todoRepository = $todoRepository;
        $this->paginator = $paginator;
    }

    /**
     * Save
     *
     * @param Todo $todo
     * @return void
     */
    public function save(Todo $todo): void
    {
        $this->todoRepository->save($todo);
    }

    /**
     * Delete
     *
     * @param Todo $todo
     * @return void
     */
    public function delete(Todo $todo): void
    {
        $this->todoRepository->delete($todo);
    }

    /**
     * Get paginated list
     *
     * @param int $page
     * @param array $filters
     * @param UserInterface|null $user
     * @return PaginationInterface
     */
    public function getPaginatedList(int $page, array $filters = [], UserInterface $user = null): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->todoRepository->queryAll($filters, $user),
            $page,
            TodoRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Find one by Id
     *
     * @param int $id
     * @return Todo
     */
    public function findOneById(int $id): Todo
    {
        return $this->todoRepository->find($id);
    }
}