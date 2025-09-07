<?php

namespace App\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CalculatorController extends AbstractController
{
    #[Route('/calculator', name: 'app_calculator')]
    public function index(): Response
    {
        return $this->render('site/calculator/index.html.twig', [
            'controller_name' => 'CalculatorController',
        ]);
    }
}
