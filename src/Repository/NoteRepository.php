<?php
/**
 * Note repository
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class NoteRepository
 *
 * @extends ServiceEntityRepository<Note>
 *
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    /**
     * Save
     *
     * @param Note $note
     */
    public function save(Note $note): void
    {
        $this->_em->persist($note);
        $this->_em->flush();
    }

    /**
     * Delete
     *
     * @param Note $note
     */
    public function delete(Note $note): void
    {
        $this->_em->remove($note);
        $this->_em->flush();
    }

    /**
     * Count by category
     *
     * @param Category $category
     *
     * @return mixed
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): mixed
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('note.id'))
            ->where('note.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Query notes by author.
     *
     * @param UserInterface         $user    User entity
     * @param array<string, object> $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(UserInterface $user, array $filters = []): QueryBuilder
    {
        $queryBuilder = $this->queryAll($filters);

        $queryBuilder
            ->andWhere('note.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
    }

    /**
     * Get all notes.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    public function queryAll(array $filters): QueryBuilder
    {
        $result = $this
            ->getOrCreateQueryBuilder()
            ->select(
                'partial note.{id, createdAt, updatedAt, title}',
                'partial category.{id, name}',
                'partial tags.{id, name}'
            )
            ->join('note.category', 'category')
            ->leftJoin('note.tags', 'tags')
            ->orderBy('note.createdAt', 'DESC');

        return $this->filter($result, $filters);
    }

    /**
     * Filter.
     *
     * @param QueryBuilder $queryBuilder
     * @param array        $filters
     *
     * @return QueryBuilder
     */
    private function filter(QueryBuilder $queryBuilder, array $filters = []): QueryBuilder
    {
        if (isset($filters['category']) && $filters['category'] instanceof Category) {
            $queryBuilder->andWhere('category = :category')->setParameter('category', $filters['category']);
        }

        if (isset($filters['tag']) && $filters['tag'] instanceof Tag) {
            $queryBuilder->andWhere('tags IN (:tag)')->setParameter('tag', $filters['tag']);
        }

        return $queryBuilder;
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('note');
    }
}
