<?php

namespace App\Controller\Admin;

use App\Entity\Ceiling;
use BcMath\Number;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CeilingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ceiling::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            MoneyField::new('value', 'Plafond')->setStoredAsCents(true)->setCurrency('EUR')->setColumns(3),
            NumberField::new('rate', 'Taux')->setColumns(3),
            TextField::new('name', 'Nom')->setColumns(6)->setDisabled(true)->setRequired(false),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof Ceiling) {

            if($entityInstance->getValue() == -100) {
                $value = 'Sans limites: ';
            }else{
                $value = $entityInstance->getValue() / 100 . '€';
            }

            $name = $value . ' (' . $entityInstance->getRate() . '%)';

            $entityInstance->setName($name);

            parent::persistEntity($entityManager, $entityInstance);
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof Ceiling) {

            if($entityInstance->getValue() == -100) {
                $value = 'Sans limites: ';
            }else{
                $value = $entityInstance->getValue() / 100 . '€';
            }

            $name = $value . ' (' . $entityInstance->getRate() . '%)';

            $entityInstance->setName($name);

            parent::persistEntity($entityManager, $entityInstance);
        }
    }
}
