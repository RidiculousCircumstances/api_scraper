<?php

namespace App\Controller\Admin;

use App\Entity\DataSchema;
use App\Entity\RequestParameter;
use App\Repository\DataSchema\DataSchemaRepository;
use App\Repository\DataSchema\Modifier\GroupModifier;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class RequestParameterCrudController extends BaseCrudController
{

    public function __construct() {}

    public static function getEntityFqcn(): string
    {
        return RequestParameter::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new("key", "ключ");
        yield TextField::new("value", "значение")->setHelp("Значение или ключ поля внешнего запроса, если выбран запрос.");

        if($this->isNew()) {
            return AssociationField::new("externalSchema", "Связанный запрос");
        }
        /**
         * @var DataSchema $dataSchema
         */
        $dataSchema = $this->getContext()?->getEntity()?->getInstance();
        $groupModifier = new GroupModifier($dataSchema->getGroupTag(), 'entity');
        yield AssociationField::new("externalSchema", "Связанный запрос")
            ->setQueryBuilder(fn(QueryBuilder $builder) =>
                $groupModifier->apply($builder)
            );
    }
}
