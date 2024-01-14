<?php

namespace App\Repository\OutputSchema;

use App\Entity\GroupTag;
use App\Entity\OutputSchema;
use App\Repository\Common\Modifier\ModifierManager;
use App\Repository\OutputSchema\Modifier\GroupModifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

    public const ALIAS = 'outputSchema';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OutputSchema::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByGroup(GroupTag $groupTag): OutputSchema|null
    {

        $qb = $this->createQueryBuilder(self::ALIAS);

        $modifierManager = new ModifierManager();

        $modifierManager->add(new GroupModifier($groupTag))->apply($qb);

        return $qb->getQuery()->getOneOrNullResult();
    }

}
