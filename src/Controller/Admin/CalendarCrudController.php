<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CalendarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Calendar::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('categorie.nom','Catégorie'),
            TextField::new('title','Titre'),
            DateTimeField::new('start','Début')->setFormat('Y-MM-dd HH:mm'),
            DateTimeField::new('end','Fin')->setFormat('Y-MM-dd HH:mm'),

        ];
    }
    
}
