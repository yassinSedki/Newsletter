<?php

namespace App\Repository;

use App\Entity\EmailTracking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailTracking>
 *
 * @method EmailTracking|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailTracking|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailTracking[]    findAll()
 * @method EmailTracking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailTrackingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailTracking::class);
    }

//    /**
//     * @return EmailTracking[] Returns an array of EmailTracking objects
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

//    public function findOneBySomeField($value): ?EmailTracking
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
