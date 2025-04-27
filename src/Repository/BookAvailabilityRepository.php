<?php

namespace App\Repository;

use App\Entity\BookAvailability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookAvailability>
 *
 * @method BookAvailability|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookAvailability|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookAvailability[]    findAll()
 * @method BookAvailability[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookAvailabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookAvailability::class);
    }

//    /**
//     * @return BookAvailability[] Returns an array of BookAvailability objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BookAvailability
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
