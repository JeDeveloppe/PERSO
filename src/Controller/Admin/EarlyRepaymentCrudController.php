<?php

namespace App\Controller\Admin;

use App\Entity\EarlyRepayment;
use App\Service\EarlyRepaymentService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class EarlyRepaymentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EarlyRepayment::class;
    }

    public function __construct(
        private EarlyRepaymentService $earlyRepaymentService
    )
    {
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('investment', 'Projet'),
            MoneyField::new('value', 'Valeur')->setCurrency('EUR')->setStoredAsCents(true),
            DateTimeField::new('createdAt', 'Date de paiement')->setColumns(3),
            MoneyField::new('remainingCapital', 'Capital restant')->setCurrency('EUR')->setStoredAsCents(true)->setRequired(false)->setDisabled(true)->hideOnForm(),
            MoneyField::new('remainingInterestByMonth', 'Intérêts par mois (recalculer)')->setCurrency('EUR')->setStoredAsCents(true)->setRequired(false)->setDisabled(true),
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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof EarlyRepayment) {
            $investment = $entityInstance->getInvestment();

            $remainingCapitalNow = $this->earlyRepaymentService->calculateRemainingCapital($investment);
            $remainingCapital = $remainingCapitalNow - $entityInstance->getValue();

            if($remainingCapital < 0) {
                $this->addFlash('warning', 'Le capital restant ne peut pas aller en dessous de 0');
                return;
                dd('STOP');
            }

            $remainingInterestByMonth = $this->earlyRepaymentService->calculateRemainingInterrestByMonth($entityInstance->getInvestment());
            $entityInstance->setRemainingInterestByMonth($remainingInterestByMonth);

            $entityInstance->setRemainingCapital($remainingCapital);

            parent::persistEntity($entityManager, $entityInstance);

            //?on met à jour le capital recu par l'investisseur et les interets recu depuis le debut de l'investissement
            $this->earlyRepaymentService->updateCapitalAlreadyReceived($investment);
            $this->earlyRepaymentService->updateTotalInterestReceived($investment);
        }
    }
}
