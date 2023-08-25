<?php
/**
 * Todo repository
 */

namespace App\Repository;

use App\Entity\Todo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TodoRepository
 *
 * @extends ServiceEntityRepository<Todo>
 *
 * @method Todo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Todo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Todo[]    findAll()
 * @method Todo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoRepository extends ServiceEntityRepository
{
    /**
     * Items per page
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Save
     *
     * @param Todo $todo
     * @return void
     */
    public function save(Todo $todo): void
    {
        $this->_em->persist($todo);
        $this->_em->flush();
    }

    /**
     * Delete
     *
     * @param Todo $todo
     * @return void
     */
    public function delete(Todo $todo): void
    {
        $this->_em->remove($todo);
        $this->_em->flush();
    }

    /**
     * Constructor
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Todo::class);
    }
}
