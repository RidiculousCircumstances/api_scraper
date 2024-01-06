<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

abstract class BaseCrudController extends AbstractCrudController
{

    public function isNew(): bool
    {
        return !$this->getContext()?->getEntity()->getInstance()?->getId();
    }

}