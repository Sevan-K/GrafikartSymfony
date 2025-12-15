<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
    }

    /**
     * Summary of paginateRecipes
     * @param int $page
     * @param string $titleFilter
     * @return PaginationInterface<int, mixed>
     */
    public function paginateRecipes(int $page, string $titleFilter): PaginationInterface
    {
        // return new Paginator(
        //     $this->createQueryBuilder('r')
        //         ->setFirstResult(($page - 1) * $limit)
        //         ->setMaxResults($limit)
        //         ->getQuery()
        //         ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
        //     false
        // );

        $query = $this->createQueryBuilder('r');

        if ($titleFilter !== '') {
            $query->andWhere("r.title LIKE :filterValue")
                ->setParameter('filterValue', '%' . $titleFilter . '%');
        }
        $query
            ->leftJoin('r.category', 'c')
            ->addSelect('c');

        return $this->paginator->paginate(
            $query,
            $page,
            2,
            [
                'distinct' => false,
                'sortFieldAllowList' => ['r.id', 'r.title']
            ]
        );
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
