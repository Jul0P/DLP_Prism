<?php

namespace App\Controller;

use App\Entity\Stage;
use App\Repository\StageRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\EtudiantRepository;
use App\Repository\SpecialiteRepository;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StageController extends AbstractController
{
    #[Route('/stage', name: 'app_stage')]
    public function index(Request $request, StageRepository $stageRepository, EntrepriseRepository $entrepriseRepository, EtudiantRepository $etudiantRepository, SpecialiteRepository $specialiteRepository, PersonneRepository $personneRepository): Response
    {
        // Récupérer le terme de recherche depuis la requête
        $search = $request->query->get('search', '');

        // Si un terme de recherche est fourni, filtrer les stages
        if ($search) {
            $stages = $stageRepository->createQueryBuilder('st')
                ->leftJoin('st.etudiant', 'et')
                ->leftJoin('st.entreprise', 'e')
                ->leftJoin('st.specialite', 's')
                ->leftJoin('st.employe', 'p')
                ->where('st.id LIKE :search')
                ->orWhere('st.dateDebut LIKE :search')
                ->orWhere('st.dateFin LIKE :search')
                ->orWhere('et.nom LIKE :search')
                ->orWhere('et.prenom LIKE :search')
                ->orWhere('e.rs LIKE :search')
                ->orWhere('s.nom LIKE :search')
                ->orWhere('p.nom LIKE :search')
                ->orWhere('p.prenom LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->distinct()
                ->getQuery()
                ->getResult();
        } else {
            // Sinon, récupérer tous les stages
            $stages = $stageRepository->findAll();
        }

        $entreprises = $entrepriseRepository->findAll();
        $etudiants = $etudiantRepository->findAll();
        $specialites = $specialiteRepository->findAll();
        $personnes = $personneRepository->findAll();

        return $this->render('dashboard/stage.html.twig', [
            'search' => $search,
            'stages' => $stages,
            'allEntreprises' => $entreprises,
            'allEtudiants' => $etudiants,
            'allSpecialites' => $specialites,
            'allPersonnes' => $personnes,
        ]);
    }

    #[Route('/stage/create', name: 'app_stage_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, EntrepriseRepository $entrepriseRepository, EtudiantRepository $etudiantRepository, SpecialiteRepository $specialiteRepository, PersonneRepository $personneRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $stage = new Stage();
            $dateDebut = \DateTime::createFromFormat('Y-m-d', $request->request->get('date_debut'));
            $dateFin = \DateTime::createFromFormat('Y-m-d', $request->request->get('date_fin'));
            $stage->setDateDebut($dateDebut ?: null);
            $stage->setDateFin($dateFin ?: null);

            $entrepriseId = $request->request->get('entreprise');
            $entreprise = $entrepriseId ? $entrepriseRepository->find($entrepriseId) : null;
            $stage->setEntreprise($entreprise);

            $etudiantId = $request->request->get('etudiant');
            $etudiant = $etudiantId ? $etudiantRepository->find($etudiantId) : null;
            $stage->setEtudiant($etudiant);

            $specialiteId = $request->request->get('specialite');
            $specialite = $specialiteId ? $specialiteRepository->find($specialiteId) : null;
            $stage->setSpecialite($specialite);

            $personneId = $request->request->get('personne');
            $personne = $personneId ? $personneRepository->find($personneId) : null;
            $stage->setEmploye($personne);

            $entityManager->persist($stage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stage');
    }

    #[Route('/stage/{id}/edit', name: 'app_stage_edit', methods: ['POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager, EntrepriseRepository $entrepriseRepository, EtudiantRepository $etudiantRepository, SpecialiteRepository $specialiteRepository, PersonneRepository $personneRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $stage = $entityManager->getRepository(Stage::class)->find($id);
        if (!$stage) {
            throw $this->createNotFoundException('Aucun stage trouvé pour cet ID : ' . $id);
        }

        if ($request->isMethod('POST')) {
            $dateDebut = \DateTime::createFromFormat('Y-m-d', $request->request->get('date_debut'));
            $dateFin = \DateTime::createFromFormat('Y-m-d', $request->request->get('date_fin'));
            $stage->setDateDebut($dateDebut ?: null);
            $stage->setDateFin($dateFin ?: null);

            $entrepriseId = $request->request->get('entreprise');
            $entreprise = $entrepriseId ? $entrepriseRepository->find($entrepriseId) : null;
            $stage->setEntreprise($entreprise);

            $etudiantId = $request->request->get('etudiant');
            $etudiant = $etudiantId ? $etudiantRepository->find($etudiantId) : null;
            $stage->setEtudiant($etudiant);

            $specialiteId = $request->request->get('specialite');
            $specialite = $specialiteId ? $specialiteRepository->find($specialiteId) : null;
            $stage->setSpecialite($specialite);

            $personneId = $request->request->get('personne');
            $personne = $personneId ? $personneRepository->find($personneId) : null;
            $stage->setEmploye($personne);

            $entityManager->persist($stage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stage');
    }

    #[Route('/stage/{id}/delete', name: 'app_stage_delete', methods: ['POST'])]
    public function delete(Request $request, Stage $stage, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $stage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($stage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stage');
    }
}