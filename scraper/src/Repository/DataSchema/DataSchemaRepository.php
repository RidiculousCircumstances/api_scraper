<?php

namespace App\Repository\DataSchema;

use App\Entity\DataSchema;
use App\Entity\GroupTag;
use App\Repository\Common\Modifier\ModifierManager;
use App\Repository\DataSchema\Modifier\ExcludeByIdsModifier;
use App\Repository\DataSchema\Modifier\GroupModifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DataSchema>
 *
 * @method DataSchema|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataSchema|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataSchema[]    findAll()
 * @method DataSchema[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataSchemaRepository extends ServiceEntityRepository
{

    public const ALIAS = 'dataSchema';

    public function __construct(ManagerRegistry $registry, private readonly ModifierManager $modifierManager)
    {
        parent::__construct($registry, DataSchema::class);
    }

    public function findByGroupTag(GroupTag $groupTag): mixed
    {
        $groupTagModifier = new GroupModifier($groupTag);

        $qb = $this->createQueryBuilder(self::ALIAS);

        $this->modifierManager->add($groupTagModifier)->apply($qb);

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findByGroupTagExcludingById(GroupTag $groupTag, array $excludeIds): mixed
    {

        $qb = $this->createQueryBuilder(self::ALIAS);

        $this->modifierManager
            ->add(new GroupModifier($groupTag))
            ->add(new ExcludeByIdsModifier($excludeIds))
            ->apply($qb);

        return $qb
            ->getQuery()
            ->getResult();
    }
}
