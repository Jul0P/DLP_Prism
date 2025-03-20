<?php

namespace App\Repository;

use App\Entity\Entreprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entreprise>
 */
class EntrepriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entreprise::class);
    }

    // Méthode pour rechercher les entreprises par raison sociale
    public function findByRaisonSociale(string $rs): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.rs LIKE :rs') // Filtrer par raison sociale avec une clause LIKE
            ->setParameter('rs', '%' . $rs . '%') // Utiliser des jokers pour la recherche partielle
            ->orderBy('e.rs', 'ASC') // Trier les résultats par raison sociale en ordre croissant
            ->getQuery()
            ->getResult(); // Exécuter la requête et retourner les résultats
    }
}
