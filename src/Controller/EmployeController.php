<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Repository\PersonneRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/employe', name: 'app_employe')]
    public function index(Request $request, PersonneRepository $personneRepository, EntrepriseRepository $entrepriseRepository, ProfilRepository $profilRepository): Response
    {
        // Récupérer le terme de recherche depuis la requête
        $search = $request->query->get('search', '');

        // Si recherche, filtrer les employés, Sinon, récupérer tous les employés
        $search ? $employes = $personneRepository->findBySearch($search) : $employes = $personneRepository->findAll();

        return $this->render('dashboard/employe.html.twig', [
            'search' => $search,
            'employes' => $employes,
            'allEntreprises' => $entrepriseRepository->findAll(),
            'allProfils' => $profilRepository->findAll(),
        ]);
    }

    #[Route('/employe/create', name: 'app_employe_create', methods: ['POST'])]
    public function create(Request $request, EntrepriseRepository $entrepriseRepository, ProfilRepository $profilRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $employe = new Personne();
            $employe->setNom($request->request->get('nom'));
            $employe->setPrenom($request->request->get('prenom'));
            $employe->setFonction($request->request->get('fonction'));
            $employe->setEmail($request->request->get('email'));
            $employe->setTel($request->request->get('tel'));

            $entrepriseId = $request->request->get('entreprise');
            $entreprise = $entrepriseId ? $entrepriseRepository->find($entrepriseId) : null;
            $employe->setEntreprise($entreprise);

            $profilIds = $request->request->all('profils') ?: [];
            $profils = $profilRepository->findBy(['id' => $profilIds]);
            foreach ($profils as $profil) {
                $employe->addprofil($profil);
            }

            $this->entityManager->persist($employe);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_employe');
    }

    #[Route('/employe/{id}/edit', name: 'app_employe_edit', methods: ['POST'])]
    public function edit(Request $request, int $id, EntrepriseRepository $entrepriseRepository, ProfilRepository $profilRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $employe = $this->entityManager->getRepository(Personne::class)->find($id);
        if (!$employe) {
            throw $this->createNotFoundException('Aucun employé trouvé pour cet ID : ' . $id);
        }

        if ($request->isMethod('POST')) {
            $employe->setNom($request->request->get('nom'));
            $employe->setPrenom($request->request->get('prenom'));
            $employe->setFonction($request->request->get('fonction'));
            $employe->setEmail($request->request->get('email'));
            $employe->setTel($request->request->get('tel'));

            $entrepriseId = $request->request->get('entreprise');
            $entreprise = $entrepriseId ? $entrepriseRepository->find($entrepriseId) : null;
            $employe->setEntreprise($entreprise);

            $profilIds = $request->request->all('profils') ?: [];
            $profils = $profilRepository->findBy(['id' => $profilIds]);
            $employe->getprofils()->clear();
            foreach ($profils as $profil) {
                $employe->addprofil($profil);
            }

            $this->entityManager->persist($employe);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_employe');
    }

    #[Route('/employe/{id}/delete', name: 'app_employe_delete', methods: ['POST'])]
    public function delete(Request $request, Personne $employe): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $employe->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($employe);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_employe');
    }
}