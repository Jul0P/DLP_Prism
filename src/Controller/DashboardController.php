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
            'totalEntreprises' => $entrepriseRepository->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->getQuery()
                ->getSingleScalarResult(),
            'totalEtudiants' => $etudiantRepository->createQueryBuilder('et')
                ->select('COUNT(et.id)')
                ->getQuery()
                ->getSingleScalarResult(),
            'totalStages' => $stageRepository->createQueryBuilder('st')
                ->select('COUNT(st.id)')
                ->getQuery()
                ->getSingleScalarResult(),
            'totalTuteurs' => $personneRepository->createQueryBuilder('p')
                ->leftJoin('p.profils', 'pr')
                ->where('pr.nom = :tuteur')
                ->setParameter('tuteur', 'Tuteur')
                ->select('COUNT(DISTINCT p.id)')
                ->getQuery()
                ->getSingleScalarResult(),
            'activeStages' => $stageRepository->createQueryBuilder('st')
                ->where('st.dateFin >= :today AND st.dateDebut <= :today')
                ->setParameter('today', new \DateTime())
                ->select('COUNT(st.id)')
                ->getQuery()
                ->getSingleScalarResult(),
        ];

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}