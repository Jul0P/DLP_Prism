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
use App\Repository\PersonneRepository;
use App\Entity\Profil;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function dashboard(Request $request, EntrepriseRepository $entrepriseRepository, SpecialiteRepository $specialiteRepository, EtudiantRepository $etudiantRepository, PersonneRepository $personneRepository): Response
    {
        // Récupérer le terme de recherche depuis la requête
        $search = $request->query->get('search', '');
        $filters = $request->query->all('filters');
        $filters = array_filter($filters, 'is_scalar');

        $allSpecialites = $specialiteRepository->findAll();
        $allEtudiants = $etudiantRepository->findAll();
        $allPersonnes = $personneRepository->findAll();

        $options = [
            ['value' => 'Rouen', 'label' => 'Rouen'],
            ['value' => 'Friville', 'label' => 'Friville'],
            ['value' => 'Mont-Saint-Aignan', 'label' => 'Mont-Saint-Aignan'],
            ['value' => 'Dreux Cedex', 'label' => 'Dreux Cedex'],
        ];

        $facets = [
            'Rouen' => 1,
            'Friville' => 1,
            'Mont-Saint-Aignan' => 1,
            'Dreux Cedex' => 1,
        ];

        $entreprises = $entrepriseRepository->findBySearchAndFilters($search, $filters);

        return $this->render('dashboard/index.html.twig', [
            'entreprises' => $entreprises,
            'selectedValues' => $filters,
            'search' => $search,
            'options' => $options,
            'facets' => $facets,
            'title' => 'Ville',
            'allPersonnes' => $allPersonnes,
        ]);
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
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/entreprise/{id}/edit', name: 'app_entreprise_edit', methods: ['POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager, PersonneRepository $personneRepository, EtudiantRepository $etudiantRepository, SpecialiteRepository $specialiteRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupérer l'entreprise à partir de l'ID
        $entreprise = $entityManager->getRepository(Entreprise::class)->find($id);
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

            // Mettre à jour les tuteurs
            $tuteurIds = $request->request->all('tuteurs'); // Utiliser all() pour récupérer un tableau
            $tuteurs = $personneRepository->findBy(['id' => $tuteurIds]);

            // Supprime les anciens tuteurs
            foreach ($entreprise->getPersonnes() as $tuteur) {
                if (!in_array($tuteur, $tuteurs)) {
                    $entreprise->removePersonne($tuteur);
                }
            }

            // Ajoute les nouveaux tuteurs
            foreach ($tuteurs as $tuteur) {
                $entreprise->addPersonne($tuteur);
            }

            $entityManager->persist($entreprise);
            $entityManager->flush();
        }

        // Rediriger vers la page du tableau de bord
        return $this->redirectToRoute('app_dashboard');
    }
}
