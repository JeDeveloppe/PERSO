<?php

namespace App\Controller\Admin;

use DateTime;
use DateTimeZone;
use DateTimeImmutable;
use App\Entity\Investment;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Validator\Constraints\Date;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InvestmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Investment::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addFieldset('Actions / Pramètres'),
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            TextField::new('name', 'Nom du projet')->setColumns(6),
            DateTimeField::new('buyAt', 'Date d\'achat')->setColumns(3)->hideOnIndex()->setFormat('dd/MM/yyyy'),
            MoneyField::new('startingCapital','Capital investie')->setCurrency('EUR')->setStoredAsCents(true)->setColumns(3),
            NumberField::new('rate', 'Taux d\'interet')->setColumns(3),
            MoneyField::new('interestByMonth', 'Interet par mois')->setCurrency('EUR')->setStoredAsCents(true)->setColumns(3)->setRequired(false),
            IntegerField::new('paymentDate', 'Date des réglements mensuels')->setColumns(3)->hideOnIndex(),
            IntegerField::new('duration', 'Durée du projet (en mois)')->setColumns(4),
            DateTimeField::new('startAt', 'Date de début')->setColumns(4)->hideOnIndex()->setFormat('dd/MM/yyyy'),
            DateTimeField::new('endAt', 'Date de fin')->setColumns(4)->setFormat('dd/MM/yyyy'),

        ];
    }
    

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['buyAt' => 'DESC'])
            ->setPageTitle('index', 'Liste des investissements')
            ->setPageTitle('new', 'Nouvel investissement')
            ->setPageTitle('edit', 'Gestion d\'un investissement')
            ->showEntityActionsInlined();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Investment) {

            //?on calcul les interets par mois en fonction du capital investite et du taux d'interet seulement s'il n'y a pas de valeur renseignée
            if($entityInstance->getInterestByMonth() == null || $entityInstance->getInterestByMonth() == 0){
                
                $interestByMonth = $entityInstance->getStartingCapital() * $entityInstance->getRate() / 100 / 12;
                $entityInstance->setInterestByMonth($interestByMonth);
            }
            //?on met à jour la durée de l'investissement
            $durationInMonthsForCalculation = $entityInstance->getDuration() - 1;
            $dateFin = $entityInstance->getStartAt()->modify('+' . $durationInMonthsForCalculation . ' months');
            $entityInstance->setEndAt($dateFin);

            //?si le mois en cours dépasse la date de fin de l'investissement à la date prévue, on met fin à l'investissement
            $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));
            if($now > $entityInstance->getEndAt()){
                $entityInstance->setIsFinished(true);
            }
            
            //?on met à jour la durée de l'investissement
            $paymentDay = $entityInstance->getPaymentDate();
            //? On s'assure que le fuseau horaire est bien défini avant de manipuler la date
            $timezone = new DateTimeZone('Europe/Paris');
            
            $dateFin = $entityInstance->getStartAt()
                ->setTimezone($timezone)
                ->modify('+' . $durationInMonthsForCalculation . ' months')
                ->setDate(
                    $entityInstance->getStartAt()->modify('+' . $durationInMonthsForCalculation . ' months')->format('Y'),
                    $entityInstance->getStartAt()->modify('+' . $durationInMonthsForCalculation . ' months')->format('m'),
                    $paymentDay
                );
            $entityInstance->setEndAt($dateFin);
            $entityInstance->setTotalInterestReceived(0);
            $entityInstance->setIsFinished(false);

            $entityManager->persist($entityInstance);

            $entityManager->flush();
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Investment) {

            //?on calcul les interets par mois en fonction du capital investite et du taux d'interet seulement s'il n'y a pas de valeur renseignée
            if($entityInstance->getInterestByMonth() == null || $entityInstance->getInterestByMonth() == 0){
                
                $interestByMonth = $entityInstance->getStartingCapital() * $entityInstance->getRate() / 100 / 12;
                $entityInstance->setInterestByMonth($interestByMonth);
            }

            //?si le mois en cours dépasse la date de fin de l'investissement à la date prévue, on met fin à l'investissement
            $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));
            if($now > $entityInstance->getEndAt()){
                $entityInstance->setIsFinished(true);
            }

            //?on met à jour la durée de l'investissement
            $paymentDay = $entityInstance->getPaymentDate();
            $durationInMonthsForCalculation = $entityInstance->getDuration() - 1;

            //? On s'assure que le fuseau horaire est bien défini avant de manipuler la date
            $timezone = new DateTimeZone('Europe/Paris');
            
            $dateFin = $entityInstance->getStartAt()
                ->setTimezone($timezone)
                ->modify('+' . $durationInMonthsForCalculation . ' months')
                ->setDate(
                    $entityInstance->getStartAt()->modify('+' . $durationInMonthsForCalculation . ' months')->format('Y'),
                    $entityInstance->getStartAt()->modify('+' . $durationInMonthsForCalculation . ' months')->format('m'),
                    $paymentDay
                );
            $entityInstance->setEndAt($dateFin);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}
