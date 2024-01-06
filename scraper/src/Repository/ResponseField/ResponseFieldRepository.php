<?php

namespace App\Repository\ResponseField;

use App\Entity\GroupTag;
use App\Entity\ResponseField;
use App\Repository\Common\Modifier\ModifierManager;
use App\Repository\ResponseField\Modifier\GroupModifier;
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
class ResponseFieldRepository extends ServiceEntityRepository
{

    public const ALIAS = 'responseField';

    public function __construct(ManagerRegistry $registry, private readonly ModifierManager $modifierManager)
    {
        parent::__construct($registry, ResponseField::class);
    }

    public function findByGroupTag(GroupTag $tag): mixed
    {

        $qb = $this->createQueryBuilder(self::ALIAS);
        $this->modifierManager->add(new GroupModifier($tag))->apply($qb);

        return $qb
            ->getQuery()
            ->getResult();
    }

}
