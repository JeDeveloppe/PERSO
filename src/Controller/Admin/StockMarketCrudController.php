<?php

namespace App\Controller\Admin;

use App\Entity\StockMarket;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class StockMarketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StockMarket::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            MoneyField::new('purchasePriceIncludingTVA', 'Prix d\'achat')->setCurrency('EUR')->setStoredAsCents(true),
            IntegerField::new('quantity', 'Quantité'),
            TextField::new('symbol', 'Symbole'),
            TextField::new('isinCode', 'Code ISIN'),

        ];
    }
    
}
