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
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/', name: 'app_entreprise')]
    public function index(Request $request, EntrepriseRepository $entrepriseRepository, PersonneRepository $personneRepository, PaysRepository $paysRepository, SpecialiteRepository $specialiteRepository): Response
    {
        $search = $request->query->get('search', '');

        $search ? $entreprises = $entrepriseRepository->findBySearch($search) : $entreprises = $entrepriseRepository->findAll();

        return $this->render('dashboard/entreprise.html.twig', [
            'entreprises' => $entreprises,
            'search' => $search,
            'allPersonnes' => $personneRepository->findAll(),
            'allPays' => $paysRepository->findAll(),
            'allSpecialites' => $specialiteRepository->findAll(),
        ]);
    }

    #[Route('/entreprise/create', name: 'app_entreprise_create', methods: ['POST'])]
    public function create(Request $request, PaysRepository $paysRepository, PersonneRepository $personneRepository, SpecialiteRepository $specialiteRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $entreprise = new Entreprise();
            $entreprise->setRs($request->request->get('rs'));
            $entreprise->setRue($request->request->get('rue'));
            $entreprise->setCp($request->request->get('cp'));
            $entreprise->setVille($request->request->get('ville'));
            $entreprise->setTel($request->request->get('tel'));
            $entreprise->setMail($request->request->get('mail'));

            $paysId = $request->request->get('pays');
            $pays = $paysId ? $paysRepository->find($paysId) : null;
            if ($paysId && !$pays) {
                // Gérer le cas où l'ID du pays est invalide
                throw new \Exception("Pays avec l'ID $paysId non trouvé.");
            }
            $entreprise->setPays($pays);

            $employeIds = $request->request->all('employes'); 
            $employes = $personneRepository->findBy(['id' => $employeIds]);
            foreach ($employes as $employe) {
                $entreprise->addPersonne($employe);
            }

            $specialiteIds = $request->request->all('profils') ?: [];
            $specialites = $specialiteRepository->findBy(['id' => $specialiteIds]);
            foreach ($specialites as $specialite) {
                $entreprise->addSpecialite($specialite);
            }

            $this->entityManager->persist($entreprise);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_entreprise');
    }

    #[Route('/entreprise/{id}/delete', name: 'app_entreprise_delete', methods: ['POST'])]
    public function delete(Request $request, Entreprise $entreprise): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$entreprise->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($entreprise);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_entreprise');
    }

    #[Route('/entreprise/{id}/edit', name: 'app_entreprise_edit', methods: ['POST'])]
    public function edit(Request $request, int $id, EntrepriseRepository $entrepriseRepository, PersonneRepository $personneRepository, PaysRepository $paysRepository, SpecialiteRepository $specialiteRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entreprise = $entrepriseRepository->find($id);
        if (!$entreprise) {
            throw $this->createNotFoundException('Aucune entreprise trouvée pour cet ID : '.$id);
        }

        if ($request->isMethod('POST')) {
            $entreprise->setRs($request->request->get('rs'));
            $entreprise->setRue($request->request->get('rue'));
            $entreprise->setCp($request->request->get('cp'));
            $entreprise->setVille($request->request->get('ville'));
            $entreprise->setTel($request->request->get('tel'));
            $entreprise->setMail($request->request->get('mail'));

            $paysId = $request->request->get('pays');
            $pays = $paysId ? $paysRepository->find($paysId) : null;
            if ($paysId && !$pays) {
                // Gérer le cas où l'ID du pays est invalide
                throw new \Exception("Pays avec l'ID $paysId non trouvé.");
            }
            $entreprise->setPays($pays);

            $employeIds = $request->request->all('employes');
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

            $specialiteIds = $request->request->all('profils') ?: [];
            $specialites = $specialiteRepository->findBy(['id' => $specialiteIds]);
            $entreprise->getSpecialites()->clear();
            foreach ($specialites as $specialite) {
                $entreprise->addSpecialite($specialite);
            }

            $this->entityManager->persist($entreprise);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_entreprise');
    }
}
