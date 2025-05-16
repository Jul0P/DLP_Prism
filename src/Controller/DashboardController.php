<?php

namespace App\Controller;

use App\Repository\EntrepriseRepository;
use App\Repository\EtudiantRepository;
use App\Repository\StageRepository;
use App\Repository\PersonneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(
        EntrepriseRepository $entrepriseRepository,
        EtudiantRepository $etudiantRepository,
        StageRepository $stageRepository,
        PersonneRepository $personneRepository
    ): Response {
        $stats = [
            'totalEntreprises' => $entrepriseRepository->countAll(),
            'totalEtudiants' => $etudiantRepository->countAll(),
            'totalStages' => $stageRepository->countAll(),
            'totalTuteurs' => $personneRepository->countTuteurs(),
            'activeStages' => $stageRepository->countActive(),
        ];

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}