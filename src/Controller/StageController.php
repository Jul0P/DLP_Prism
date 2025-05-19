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
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/stage', name: 'app_stage')]
    public function index(Request $request, StageRepository $stageRepository, EntrepriseRepository $entrepriseRepository, EtudiantRepository $etudiantRepository, SpecialiteRepository $specialiteRepository, PersonneRepository $personneRepository): Response
    {
        $search = $request->query->get('search', '');

        $search ? $stages = $stageRepository->findBySearch($search) : $stages = $stageRepository->findAll();

        return $this->render('dashboard/stage.html.twig', [
            'search' => $search,
            'stages' => $stages,
            'allEntreprises' => $entrepriseRepository->findAll(),
            'allEtudiants' => $etudiantRepository->findAll(),
            'allSpecialites' => $specialiteRepository->findAll(),
            'allPersonnes' => $personneRepository->findAll(),
        ]);
    }

    #[Route('/stage/create', name: 'app_stage_create', methods: ['POST'])]
    public function create(Request $request, EntrepriseRepository $entrepriseRepository, EtudiantRepository $etudiantRepository, SpecialiteRepository $specialiteRepository, PersonneRepository $personneRepository): Response
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

            $this->entityManager->persist($stage);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_stage');
    }

    #[Route('/stage/{id}/edit', name: 'app_stage_edit', methods: ['POST'])]
    public function edit(Request $request, int $id, EntrepriseRepository $entrepriseRepository, EtudiantRepository $etudiantRepository, SpecialiteRepository $specialiteRepository, PersonneRepository $personneRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $stage = $this->entityManager->getRepository(Stage::class)->find($id);
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

            $this->entityManager->persist($stage);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_stage');
    }

    #[Route('/stage/{id}/delete', name: 'app_stage_delete', methods: ['POST'])]
    public function delete(Request $request, Stage $stage): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $stage->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($stage);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_stage');
    }
}