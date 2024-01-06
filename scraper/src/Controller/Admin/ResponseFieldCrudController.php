<?php

namespace App\Controller\Admin;

use App\Entity\ResponseField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseFieldCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ResponseField::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('dataPath', 'Ключ');
        yield TextField::new('outputName', 'Название');
    }

}
