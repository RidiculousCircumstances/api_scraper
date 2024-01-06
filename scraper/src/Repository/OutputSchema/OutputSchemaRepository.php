<?php

namespace App\Repository\OutputSchema;

use App\Entity\OutputSchema;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OutputSchema>
 *
 * @method OutputSchema|null find($id, $lockMode = null, $lockVersion = null)
 * @method OutputSchema|null findOneBy(array $criteria, array $orderBy = null)
 * @method OutputSchema[]    findAll()
 * @method OutputSchema[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutputSchemaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OutputSchema::class);
    }

}
