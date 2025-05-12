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
    #[Route('/etudiant', name: 'app_etudiant')]
    public function index(Request $request, EtudiantRepository $etudiantRepository): Response
    {
        // Récupérer le terme de recherche depuis la requête
        $search = $request->query->get('search', '');

        // Si un terme de recherche est fourni, filtrer les étudiants
        if ($search) {
            $etudiants = $etudiantRepository->createQueryBuilder('et')
                ->leftJoin('et.stages', 'st')
                ->leftJoin('st.entreprise', 'e')
                ->where('et.id LIKE :search')
                ->orWhere('et.nom LIKE :search')
                ->orWhere('et.prenom LIKE :search')
                ->orWhere('et.email LIKE :search')
                ->orWhere('et.tel LIKE :search')
                ->orWhere('e.rs LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->distinct()
                ->getQuery()
                ->getResult();
        } else {
            // Sinon, récupérer tous les étudiants
            $etudiants = $etudiantRepository->findAll();
        }

        return $this->render('dashboard/etudiant.html.twig', [
            'search' => $search,
            'etudiants' => $etudiants,
        ]);
    }

    #[Route('/etudiant/create', name: 'app_etudiant_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $etudiant = new Etudiant();
            $etudiant->setNom($request->request->get('nom'));
            $etudiant->setPrenom($request->request->get('prenom'));
            $etudiant->setEmail($request->request->get('email'));
            $etudiant->setTel($request->request->get('tel'));

            $entityManager->persist($etudiant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant');
    }

    #[Route('/etudiant/{id}/edit', name: 'app_etudiant_edit', methods: ['POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $etudiant = $entityManager->getRepository(Etudiant::class)->find($id);
        if (!$etudiant) {
            throw $this->createNotFoundException('Aucun étudiant trouvé pour cet ID : ' . $id);
        }

        if ($request->isMethod('POST')) {
            $etudiant->setNom($request->request->get('nom'));
            $etudiant->setPrenom($request->request->get('prenom'));
            $etudiant->setEmail($request->request->get('email'));
            $etudiant->setTel($request->request->get('tel'));

            $entityManager->persist($etudiant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant');
    }

    #[Route('/etudiant/{id}/delete', name: 'app_etudiant_delete', methods: ['POST'])]
    public function delete(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $etudiant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($etudiant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant');
    }
}