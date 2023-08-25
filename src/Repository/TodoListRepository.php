<?php
/**
 * Todo list repository.
 */

namespace App\Repository;

use App\Entity\TodoList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TodoListRepository.
 *
 * @extends ServiceEntityRepository<TodoList>
 *
 * @method TodoList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TodoList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TodoList[]    findAll()
 * @method TodoList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoListRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Save todo list.
     *
     * @param TodoList $todoList
     */
    public function save(TodoList $todoList): void
    {
        $this->_em->persist($todoList);
        $this->_em->flush();
    }

    /**
     * Delete
     *
     * @param TodoList $todoList
     */
    public function delete(TodoList $todoList): void
    {
        $this->_em->remove($todoList);
        $this->_em->flush();
    }

    /**
     * Constructor
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodoList::class);
    }

    /**
     * Get or create query builder
     *
     * @param QueryBuilder|null $queryBuilder
     *
     * @return QueryBuilder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('todo_list');
    }

    /**
     * Query todo lists by author
     *
     * @param UserInterface $user User entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(UserInterface $user): QueryBuilder
    {
        return $this
            ->queryAll()
            ->andWhere('todo_list.author = :author')
            ->setParameter('author', $user);
    }

    /**
     * Get all todo lists.
     *
     * @return QueryBuilder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder();
    }
}
