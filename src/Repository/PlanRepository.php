<?php

namespace App\Repository;

use App\Entity\Plan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Plan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plan[]    findAll()
 * @method Plan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plan::class);
    }

    public function countPerYear($year)
    {
        $count = $this->createQueryBuilder('p')
            ->select("count(p.id) as count")
            ->where('YEAR(p.vacationStartDate) = :year')
            ->orWhere('YEAR(p.vacationEndDate) = :year')
            ->setParameter('year', $year)
            ->getQuery()
            ->getScalarResult();

        return $count[0]["count"] ?? 0;
    }

}
