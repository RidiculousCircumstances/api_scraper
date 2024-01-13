<?php

namespace App\Controller\Admin;

use App\Entity\OutputSchema;
use App\Repository\ResponseField\Modifier\GroupModifier;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OutputSchemaCrudController extends BaseCrudController
{

    public function __construct()
    {
    }

    public static function getEntityFqcn(): string
    {
        return OutputSchema::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->setDisabled();

        $groupTag = AssociationField::new('groupTag');
        if ($this->isNew()) {
            yield $groupTag;
        } else {
            yield $groupTag->setDisabled();
        }
        yield TextField::new('name');

        if ($this->isNew()) {
            return AssociationField::new('responseFields');
        }

        $outputSchema = $this->getContext()?->getEntity()->getInstance();
        $groupTag = $outputSchema->getGroupTag();
        if (!$groupTag) {
            return AssociationField::new('responseFields');
        }

        $groupModifier = new GroupModifier($outputSchema->getGroupTag(), 'entity');

        yield AssociationField::new('responseFields')
            ->setQueryBuilder(fn(QueryBuilder $b) => $groupModifier->apply($b));
    }


}
