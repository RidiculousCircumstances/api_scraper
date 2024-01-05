<?php

namespace App\Repository;

use App\Entity\RequestParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RequestParameter>
 *
 * @method RequestParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestParameter[]    findAll()
 * @method RequestParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestParameter::class);
    }

//    /**
//     * @return RequestParameter[] Returns an array of RequestParameter objects
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

//    public function findOneBySomeField($value): ?RequestParameter
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
