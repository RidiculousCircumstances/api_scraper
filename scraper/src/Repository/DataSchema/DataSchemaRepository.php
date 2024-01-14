<?php

namespace App\Repository\DataSchema;

use App\Entity\DataSchema;
use App\Entity\GroupTag;
use App\Repository\Common\Modifier\ModifierManager;
use App\Repository\DataSchema\Modifier\ExcludeByIdsModifier;
use App\Repository\DataSchema\Modifier\GroupModifier;
use App\Repository\DataSchema\Modifier\HighPriorityModifier;
use App\Repository\DataSchema\Modifier\NotMutedModifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataSchema::class);
    }

    public function findByGroupTag(GroupTag $groupTag): mixed
    {

        $qb = $this->createQueryBuilder(self::ALIAS);

        $modifierManager = new ModifierManager();

        $modifierManager
            ->add(new GroupModifier($groupTag))
            ->apply($qb);

        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * Получить основную схему группы, с которой начнется исполнение инструкции
     * @param GroupTag $groupTag
     * @return float|int|mixed|string
     * @throws NonUniqueResultException
     */
    public function findHighPrioritySchemaByGroup(GroupTag $groupTag): mixed
    {
        $qb = $this->createQueryBuilder(self::ALIAS);
        $modifierManager = new ModifierManager();

        $modifierManager
            ->add(new GroupModifier($groupTag))
            ->add(new NotMutedModifier())
            ->add(new HighPriorityModifier())
            ->apply($qb);

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByGroupTagExcludingById(GroupTag $groupTag, array $excludeIds): mixed
    {

        $qb = $this->createQueryBuilder(self::ALIAS);
        $modifierManager = new ModifierManager();

        $modifierManager
            ->add(new GroupModifier($groupTag))
            ->add(new ExcludeByIdsModifier($excludeIds))
            ->apply($qb);

        return $qb
            ->getQuery()
            ->getResult();
    }

}
