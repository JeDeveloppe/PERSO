<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\LegalInformation;
use App\Entity\Project;
use App\Entity\Technology;
use App\Entity\TechnologyFamily;
use App\Entity\Training;
use App\Entity\Article;
use App\Entity\EarlyRepayment;
use App\Entity\Investment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('PERSO RENE');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Site', 'fa-solid fa-earth-europe', 'app_site');

        yield MenuItem::section('Investissements');
        yield MenuItem::linkToCrud('Projets', 'fas fa-list', Investment::class);
        yield MenuItem::linkToCrud('Remboursements', 'fas fa-list', EarlyRepayment::class);
    }
}
