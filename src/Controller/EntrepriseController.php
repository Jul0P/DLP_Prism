<?php
namespace App\Controller;

use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EntrepriseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SpecialiteRepository;
use App\Repository\EtudiantRepository;
use App\Repository\PaysRepository;
use App\Repository\PersonneRepository;

class EntrepriseController extends AbstractController
{
    #[Route('/', name: 'app_entreprise')]
    public function index(Request $request, EntrepriseRepository $entrepriseRepository, PersonneRepository $personneRepository, PaysRepository $paysRepository, SpecialiteRepository $specialiteRepository): Response
    {
        // Récupérer le terme de recherche depuis la requête
        $search = $request->query->get('search', '');

        // Si un terme de recherche est fourni, filtrer les entreprises
        if ($search) {
            $entreprises = $entrepriseRepository->findBySearch($search);
        } else {
            // Sinon, récupérer toutes les entreprises
            $entreprises = $entrepriseRepository->findAll();
        }

        return $this->render('dashboard/entreprise.html.twig', [
            'entreprises' => $entreprises,
            'search' => $search,
            'allPersonnes' => $personneRepository->findAll(),
            'allPays' => $paysRepository->findAll(),
            'allSpecialites' => $specialiteRepository->findAll(),
        ]);
    }

    #[Route('/entreprise/create', name: 'app_entreprise_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, PaysRepository $paysRepository, PersonneRepository $personneRepository, SpecialiteRepository $specialiteRepository): Response
    {
        // Vérifier que l'utilisateur a les droits pour créer (ROLE_ADMIN)
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            // Créer une nouvelle instance d'Entreprise
            $entreprise = new Entreprise();
            $entreprise->setRs($request->request->get('rs'));
            $entreprise->setRue($request->request->get('rue'));
            $entreprise->setCp($request->request->get('cp'));
            $entreprise->setVille($request->request->get('ville'));
            $entreprise->setTel($request->request->get('tel'));
            $entreprise->setMail($request->request->get('mail'));

            // Gérer le champ pays
            $paysId = $request->request->get('pays');
            $pays = $paysId ? $paysRepository->find($paysId) : null;
            if ($paysId && !$pays) {
                // Gérer le cas où l'ID du pays est invalide
                throw new \Exception("Pays avec l'ID $paysId non trouvé.");
            }
            $entreprise->setPays($pays);

            // Gérer les employés
            $employeIds = $request->request->all('employes'); // Utiliser all() pour récupérer un tableau
            $employes = $personneRepository->findBy(['id' => $employeIds]);
            foreach ($employes as $employe) {
                $entreprise->addPersonne($employe);
            }

            // Gérer les spécialités
            $specialiteIds = $request->request->all('profils') ?: [];
            $specialites = $specialiteRepository->findBy(['id' => $specialiteIds]);
            foreach ($specialites as $specialite) {
                $entreprise->addSpecialite($specialite);
            }

            // Enregistrer l'entreprise dans la base de données
            $entityManager->persist($entreprise);
            $entityManager->flush();
        }

        // Rediriger vers la page du tableau de bord
        return $this->redirectToRoute('app_entreprise');
    }

    #[Route('/entreprise/{id}/delete', name: 'app_entreprise_delete', methods: ['POST'])]
    public function delete(Request $request, Entreprise $entreprise, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur a les droits pour supprimer (ROLE_ADMIN)
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Vérifier le jeton CSRF pour sécuriser la suppression
        if ($this->isCsrfTokenValid('delete'.$entreprise->getId(), $request->request->get('_token'))) {
            // Supprimer l'entreprise (les relations en cascade doivent être configurées dans Doctrine)
            $entityManager->remove($entreprise);
            $entityManager->flush();
        }

        // Rediriger vers la page du tableau de bord
        return $this->redirectToRoute('app_entreprise');
    }

    #[Route('/entreprise/{id}/edit', name: 'app_entreprise_edit', methods: ['POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager, EntrepriseRepository $entrepriseRepository, PersonneRepository $personneRepository, PaysRepository $paysRepository, SpecialiteRepository $specialiteRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupérer l'entreprise à partir de l'ID
        $entreprise = $entrepriseRepository->find($id);
        if (!$entreprise) {
            throw $this->createNotFoundException('Aucune entreprise trouvée pour cet ID : '.$id);
        }

        if ($request->isMethod('POST')) {
            // Mettre à jour les champs simples
            $entreprise->setRs($request->request->get('rs'));
            $entreprise->setRue($request->request->get('rue'));
            $entreprise->setCp($request->request->get('cp'));
            $entreprise->setVille($request->request->get('ville'));
            $entreprise->setTel($request->request->get('tel'));
            $entreprise->setMail($request->request->get('mail'));

            // Gérer le champ pays
            $paysId = $request->request->get('pays');
            $pays = $paysId ? $paysRepository->find($paysId) : null;
            if ($paysId && !$pays) {
                // Gérer le cas où l'ID du pays est invalide
                throw new \Exception("Pays avec l'ID $paysId non trouvé.");
            }
            $entreprise->setPays($pays);

            // Mettre à jour les employes
            $employeIds = $request->request->all('employes'); // Utiliser all() pour récupérer un tableau
            $employes = $personneRepository->findBy(['id' => $employeIds]);

            // Supprime les anciens employes
            foreach ($entreprise->getPersonnes() as $employe) {
                if (!in_array($employe, $employes)) {
                    $entreprise->removePersonne($employe);
                }
            }

            // Ajoute les nouveaux employes
            foreach ($employes as $employe) {
                $entreprise->addPersonne($employe);
            }

            // --- Mettre à jour les profils (spécialités) ---
            $specialiteIds = $request->request->all('profils') ?: [];
            $specialites = $specialiteRepository->findBy(['id' => $specialiteIds]);
            $entreprise->getSpecialites()->clear();
            foreach ($specialites as $specialite) {
                $entreprise->addSpecialite($specialite);
            }

            $entityManager->persist($entreprise);
            $entityManager->flush();
        }

        // Rediriger vers la page du tableau de bord
        return $this->redirectToRoute('app_entreprise');
    }
}
