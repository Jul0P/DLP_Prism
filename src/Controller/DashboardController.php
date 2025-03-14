<?php

namespace App\Controller;

use App\Repository\EntrepriseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'entreprises' => $entrepriseRepository->findAll(), // On récupère toutes les entreprises
        ]);
    }
}
