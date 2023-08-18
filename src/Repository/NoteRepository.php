<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 *
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function save(Note $note): void
    {
        $this->_em->persist($note);
        $this->_em->flush();
    }

    public function delete(Note $note): void
    {
        $this->_em->remove($note);
        $this->_em->flush();
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

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
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
}
