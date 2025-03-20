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

        // Si un terme de recherche est fourni, rechercher les entreprises correspondantes
        // Sinon, récupérer toutes les entreprises
        $entreprises = $search ? $entrepriseRepository->findByRaisonSociale($search) : $entrepriseRepository->findAll();

        // Rendre le template avec les entreprises trouvées et le terme de recherche
        return $this->render('dashboard/index.html.twig', [
            'entreprises' => $entreprises,
            'search' => $search,
        ]);
    }
}
