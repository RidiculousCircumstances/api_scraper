<?php

namespace App\Controller\Admin;

use App\Entity\DataSchema;
use App\Entity\OutputSchema;
use App\Entity\RequestParameter;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Routing\Attribute\Route;

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
        if($this->isNew()) {
            yield $groupTag;
        } else {
            yield $groupTag->setDisabled();
        }

        yield TextField::new('name');
        yield TextField::new('url');

        yield CollectionField::new('requestParameters')->useEntryCrudForm();
        yield CollectionField::new('responseFields')->useEntryCrudForm();
    }
}
