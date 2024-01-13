<?php

namespace App\Controller\Admin;

use App\Entity\DataSchema;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DataSchemaCrudController extends BaseCrudController
{

    public static function getEntityFqcn(): string
    {
        return DataSchema::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->setDisabled();

        $groupTag = AssociationField::new('groupTag')->renderAsNativeWidget();
        if ($this->isNew()) {
            yield $groupTag;
        } else {
            yield $groupTag->setDisabled();
        }

        yield TextField::new('name');
        yield TextField::new('url');
        yield BooleanField::new('needs_auth');
        yield CollectionField::new('requestParameters')->useEntryCrudForm();
        yield CollectionField::new('responseFields')->useEntryCrudForm();
    }
}
