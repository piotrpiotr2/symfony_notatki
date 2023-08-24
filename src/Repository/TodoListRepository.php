<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\TodoList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<TodoList>
 *
 * @method TodoList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TodoList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TodoList[]    findAll()
 * @method TodoList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoListRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    public function save(TodoList $todoList): void
    {
        $this->_em->persist($todoList);
        $this->_em->flush();
    }

    public function delete(TodoList $todoList): void
    {
        $this->_em->remove($todoList);
        $this->_em->flush();
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodoList::class);
    }

    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('todo_list');
    }

    /**
     * Query notes by author.
     *
     * @param UserInterface $user User entity
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(UserInterface $user): QueryBuilder
    {
        return $this
            ->queryAll()
            ->andWhere('todo_list.author = :author')
            ->setParameter('author', $user);
    }

    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder();
    }

//    /**
//     * @return TodoList[] Returns an array of TodoList objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TodoList
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
