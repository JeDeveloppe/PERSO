<?php

namespace App\Controller\Admin;

use App\Entity\Account;
use phpDocumentor\Reflection\Types\Integer;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('bank', 'Banque'),
            TextField::new('name', 'Nom'),
            AssociationField::new('ceiling', 'Plafond'),
            MoneyField::new('value', 'Montant')->setCurrency('EUR')->setStoredAsCents(true),
            BooleanField::new('isAvailable', 'Disponible'),
        ];
    }
    
}
