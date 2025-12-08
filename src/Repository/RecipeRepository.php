<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /**
     * Find all recipe which duration is lower than a duration
     * @param int $duration
     * @return Recipe[]
     */
    public function findWithDurationLowerThan(int $duration): array
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'c')
            ->where('r.duration <= :duration')
            ->orderBy('r.duration', 'ASC')
            ->leftJoin('r.category', 'c')
            // ->andWhere("c.slug = 'dessert'")
            // ->andWhere("r.category = 1") // target category_id on the data base
            ->setMaxResults(10)
            ->setParameter('duration', $duration)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findTotalDuration(): int
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.duration) as total')
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
