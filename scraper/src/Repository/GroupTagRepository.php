<?php

namespace App\Repository;

use App\Entity\GroupTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupTag>
 *
 * @method GroupTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupTag[]    findAll()
 * @method GroupTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupTag::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByCode($value): GroupTag|null
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.code = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
