<?php

namespace App\Repository;

use App\Entity\Product2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product2|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product2|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product2[]    findAll()
 * @method Product2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Product2Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product2::class);
    }

    // /**
    //  * @return Product2[] Returns an array of Product2 objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product2
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
