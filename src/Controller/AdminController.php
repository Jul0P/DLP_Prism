<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Pays;
use App\Entity\Specialite;
use App\Entity\Profil;
use App\Repository\UserRepository;
use App\Repository\PaysRepository;
use App\Repository\SpecialiteRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $passwordHasher) {}

    #[Route('/admin', name: 'app_admin')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        PaysRepository $paysRepository,
        SpecialiteRepository $specialiteRepository,
        ProfilRepository $profilRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupérer le terme de recherche depuis la requête
        $search = $request->query->get('search', '');

        // Si recherche, filtrer les utilisateurs, Sinon récupérer tous les utilisateurs
        $search ? $users = $userRepository->findBySearch($search) : $users = $userRepository->findAll();

        return $this->render('dashboard/admin.html.twig', [
            'search' => $search,
            'users' => $users,
            'pays' => $paysRepository->findAll(),
            'specialites' => $specialiteRepository->findAll(),
            'profils' => $profilRepository->findAll(),
        ]);
    }

    // Création d'un utilisateur
    #[Route('/admin/user/create', name: 'app_admin_user_create', methods: ['POST'])]
    public function createUser(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $user = new User();
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
            $user->setEmail($request->request->get('email'));
            
            $roles = $request->request->all('user_roles');
            $roles = is_array($roles) && !empty($roles) ? array_unique(array_filter($roles)) : ['ROLE_USER'];
            $user->setRoles($roles);
            
            $hashedPassword = $this->passwordHasher->hashPassword($user, $request->request->get('password'));
            $user->setPassword($hashedPassword);
            // $user->setPassword(password_hash($request->request->get('password'), PASSWORD_BCRYPT));

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    // Modification d'un utilisateur
    #[Route('/admin/user/{id}/edit', name: 'app_admin_user_edit', methods: ['POST'])]
    public function editUser(Request $request, int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Aucun utilisateur trouvé pour cet ID : ' . $id);
        }

        if ($request->isMethod('POST')) {
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
            $user->setEmail($request->request->get('email'));
            
            $roles = $request->request->all('user_roles');
            $roles = is_array($roles) && !empty($roles) ? array_unique(array_filter($roles)) : ['ROLE_USER'];
            $user->setRoles($roles);

            if ($request->request->get('password')) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $request->request->get('password'));
                $user->setPassword($hashedPassword);
                // $user->setPassword(password_hash($request->request->get('password'), PASSWORD_BCRYPT));
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    // Suppression d'un utilisateur
    #[Route('/admin/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    // Création d'un pays
    #[Route('/admin/pays/create', name: 'app_admin_pays_create', methods: ['POST'])]
    public function createPays(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $pays = new Pays();
            $pays->setNom($request->request->get('nom'));

            $this->entityManager->persist($pays);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    // Modification d'un pays
    #[Route('/admin/pays/{id}/edit', name: 'app_admin_pays_edit', methods: ['POST'])]
    public function editPays(Request $request, int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pays = $this->entityManager->getRepository(Pays::class)->find($id);
        if (!$pays) {
            throw $this->createNotFoundException('Aucun pays trouvé pour cet ID : ' . $id);
        }

        if ($request->isMethod('POST')) {
            $pays->setNom($request->request->get('nom'));

            $this->entityManager->persist($pays);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    // Suppression d'un pays
    #[Route('/admin/pays/{id}/delete', name: 'app_admin_pays_delete', methods: ['POST'])]
    public function deletePays(Request $request, Pays $pays): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $pays->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($pays);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    // Création d'une spécialité
    #[Route('/admin/specialite/create', name: 'app_admin_specialite_create', methods: ['POST'])]
    public function createSpecialite(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $specialite = new Specialite();
            $specialite->setNom($request->request->get('nom'));

            $this->entityManager->persist($specialite);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    // Modification d'une spécialité
    #[Route('/admin/specialite/{id}/edit', name: 'app_admin_specialite_edit', methods: ['POST'])]
    public function editSpecialite(Request $request, int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $specialite = $this->entityManager->getRepository(Specialite::class)->find($id);
        if (!$specialite) {
            throw $this->createNotFoundException('Aucune spécialité trouvée pour cet ID : ' . $id);
        }

        if ($request->isMethod('POST')) {
            $specialite->setNom($request->request->get('nom'));

            $this->entityManager->persist($specialite);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    // Suppression d'une spécialité
    #[Route('/admin/specialite/{id}/delete', name: 'app_admin_specialite_delete', methods: ['POST'])]
    public function deleteSpecialite(Request $request, Specialite $specialite): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $specialite->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($specialite);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    // Création d'un profil
    #[Route('/admin/profil/create', name: 'app_admin_profil_create', methods: ['POST'])]
    public function createProfil(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $profil = new Profil();
            $profil->setNom($request->request->get('nom'));

            $this->entityManager->persist($profil);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    // Modification d'un profil
    #[Route('/admin/profil/{id}/edit', name: 'app_admin_profil_edit', methods: ['POST'])]
    public function editProfil(Request $request, int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $profil = $this->entityManager->getRepository(Profil::class)->find($id);
        if (!$profil) {
            throw $this->createNotFoundException('Aucun profil trouvé pour cet ID : ' . $id);
        }

        if ($request->isMethod('POST')) {
            $profil->setNom($request->request->get('nom'));

            $this->entityManager->persist($profil);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    // Suppression d'un profil
    #[Route('/admin/profil/{id}/delete', name: 'app_admin_profil_delete', methods: ['POST'])]
    public function deleteProfil(Request $request, Profil $profil): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $profil->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($profil);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }
}