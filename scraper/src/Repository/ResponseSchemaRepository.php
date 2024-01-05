<?php

namespace App\Repository;

use App\Entity\ResponseField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResponseField>
 *
 * @method ResponseField|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResponseField|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResponseField[]    findAll()
 * @method ResponseField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponseSchemaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResponseField::class);
    }

//    /**
//     * @return ResponseField[] Returns an array of ResponseField objects
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

//    public function findOneBySomeField($value): ?ResponseField
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
