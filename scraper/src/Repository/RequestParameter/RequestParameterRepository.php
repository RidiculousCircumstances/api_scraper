<?php

namespace App\Repository\RequestParameter;

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

}
