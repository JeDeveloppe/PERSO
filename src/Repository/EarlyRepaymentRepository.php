<?php

namespace App\Repository;

use App\Entity\EarlyRepayment;
use App\Entity\Investment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EarlyRepayment>
 */
class EarlyRepaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EarlyRepayment::class);
    }

    public function sumEarlyRepaymentsByInvestment(Investment $investment): int
    {
        $sum = $this->createQueryBuilder('er')
            ->select('SUM(er.value)')
            ->where('er.investment = :investment')
            ->setParameter('investment', $investment)
            ->getQuery()
            ->getSingleScalarResult();
        
        return $sum ?? 0;
    }

    public function findLastEarlyRepayment(Investment $investment): ?EarlyRepayment
    {
        return $this->getEntityManager()->getRepository(EarlyRepayment::class)
            ->findOneBy(['investment' => $investment], ['createdAt' => 'DESC']);
    }

    //    /**
    //     * @return EarlyRepayment[] Returns an array of EarlyRepayment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?EarlyRepayment
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
