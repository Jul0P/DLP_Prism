<?php
namespace App\Controller;

use App\Repository\EntrepriseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(Request $request, EntrepriseRepository $entrepriseRepository): Response
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
}
