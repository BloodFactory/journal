<?php

namespace App\Repository;

use App\Entity\UserAlert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAlert|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAlert|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAlert[]    findAll()
 * @method UserAlert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAlert::class);
    }

    // /**
    //  * @return UserAlert[] Returns an array of UserAlert objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserAlert
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
