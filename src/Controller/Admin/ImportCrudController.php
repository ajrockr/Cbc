<?php

namespace App\Controller\Admin;

use App\Entity\Import;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ImportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Import::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();

        yield DateTimeField::new('importedAt');
        yield AssociationField::new('importedBy')
            ->onlyOnIndex()
            ->autocomplete();

        yield ArrayField::new('importedData')
            ->hideOnIndex();
    }
}
