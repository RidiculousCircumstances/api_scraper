<?php

namespace App\Controller\Admin;

use App\Entity\DataSchema;
use App\Entity\RequestParameter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class RequestParameterCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return RequestParameter::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new("key", "ключ");
        yield TextField::new("value", "значение")->setHelp("Значение или ключ поля внешнего запроса, если выбран запрос.");

        /**
         * @var DataSchema $dataSchema
         */
        $dataSchema = $this->getContext()?->getEntity()?->getInstance();

        if($dataSchema === null) {
            return AssociationField::new("externalSchema", "Связанный запрос");
        }

        yield AssociationField::new("externalSchema", "Связанный запрос")
            ->setQueryBuilder(fn(QueryBuilder $builder) =>
                $builder
                    ->where('entity.groupTag = :groupTagId')
                    ->setParameters([
                        'groupTagId' => $dataSchema->getGroupTag()?->getId()
                    ])
            );
    }
}
