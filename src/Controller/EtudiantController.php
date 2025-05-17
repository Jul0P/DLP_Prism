<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtudiantController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/etudiant', name: 'app_etudiant')]
    public function index(Request $request, EtudiantRepository $etudiantRepository): Response
    {
        // Récupérer le terme de recherche depuis la requête
        $search = $request->query->get('search', '');

        // Si recherche, filtrer les étudiants, Sinon, récupérer tous les étudiants
        $search ? $etudiants = $etudiantRepository->findBySearch($search) : $etudiants = $etudiantRepository->findAll();

        return $this->render('dashboard/etudiant.html.twig', [
            'search' => $search,
            'etudiants' => $etudiants,
        ]);
    }

    #[Route('/etudiant/create', name: 'app_etudiant_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $etudiant = new Etudiant();
            $etudiant->setNom($request->request->get('nom'));
            $etudiant->setPrenom($request->request->get('prenom'));
            $etudiant->setEmail($request->request->get('email'));
            $etudiant->setTel($request->request->get('tel'));

            $this->entityManager->persist($etudiant);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant');
    }

    #[Route('/etudiant/{id}/edit', name: 'app_etudiant_edit', methods: ['POST'])]
    public function edit(Request $request, int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $etudiant = $this->entityManager->getRepository(Etudiant::class)->find($id);
        if (!$etudiant) {
            throw $this->createNotFoundException('Aucun étudiant trouvé pour cet ID : ' . $id);
        }

        if ($request->isMethod('POST')) {
            $etudiant->setNom($request->request->get('nom'));
            $etudiant->setPrenom($request->request->get('prenom'));
            $etudiant->setEmail($request->request->get('email'));
            $etudiant->setTel($request->request->get('tel'));

            $this->entityManager->persist($etudiant);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant');
    }

    #[Route('/etudiant/{id}/delete', name: 'app_etudiant_delete', methods: ['POST'])]
    public function delete(Request $request, Etudiant $etudiant): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $etudiant->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($etudiant);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant');
    }
}