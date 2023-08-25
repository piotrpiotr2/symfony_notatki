<?php
/**
 * Tag repository.
 */

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TagRepository.
 *
 * @extends ServiceEntityRepository<Tag>
 *s
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Save.
     *
     * @param Tag $tag
     */
    public function save(Tag $tag): void
    {
        $this->_em->persist($tag);
        $this->_em->flush();
    }

    /**
     * Delete tag.
     *
     * @param Tag $tag
     */
    public function delete(Tag $tag): void
    {
        $this->_em->remove($tag);
        $this->_em->flush();
    }

    /**
     * Constructor
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    //    /**
    //     * @return Tag[] Returns an array of Tag objects
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

    //    public function findOneBySomeField($value): ?Tag
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Find one tag.
     *
     * @param int $id
     *
     * @return Tag|null
     */
    public function findOneById(int $id): ?Tag
    {
        return $this->find($id);
    }
}
