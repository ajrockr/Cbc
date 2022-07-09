<?php

namespace App\Controller\Admin;

use App\Entity\RepairItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RepairItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RepairItem::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
