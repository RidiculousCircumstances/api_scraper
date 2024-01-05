<?php

namespace App\Controller\Admin;

use App\Entity\GroupTag;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GroupTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GroupTag::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('code'),
        ];
    }
}
