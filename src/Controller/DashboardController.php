<?php
namespace App\Controller;

use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EntrepriseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function dashboard(Request $request, EntrepriseRepository $entrepriseRepository): Response
    {
        // Récupérer le terme de recherche depuis la requête
        $search = $request->query->get('search', '');
        $filters = $request->query->all('filters');
        $filters = array_filter($filters, 'is_scalar');

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
}
