<?php

namespace App\Controller\Admin;

use App\Entity\EarlyRepayment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class EarlyRepaymentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EarlyRepayment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('investment', 'Projet'),
            MoneyField::new('value', 'Valeur')->setCurrency('EUR')->setStoredAsCents(true),
            DateTimeField::new('createdAt', 'Date de paiement')->setColumns(3),
        ];
    }
    

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setPageTitle('index', 'Liste des reglements anticipés')
            ->setPageTitle('new', 'Nouveau reglement anticipé')
            ->setPageTitle('edit', 'Gestion d\'un reglement anticipé')
            ->showEntityActionsInlined();
    }
}
