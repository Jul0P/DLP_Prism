<?php

namespace App\Repository;

use App\Entity\Personne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
class PersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personne::class);
    }

    public function findBySearch(string $search): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.profils', 'pr')
            ->leftJoin('p.entreprise', 'e')
            ->where('p.id LIKE :search')
            ->orWhere('p.nom LIKE :search')
            ->orWhere('p.prenom LIKE :search')
            ->orWhere('p.fonction LIKE :search')
            ->orWhere('p.email LIKE :search')
            ->orWhere('p.tel LIKE :search')
            ->orWhere('pr.nom LIKE :search')
            ->orWhere('e.rs LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->distinct();

        return $qb->getQuery()->getResult();
    }

    public function countTuteurs(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->leftJoin('p.profils', 'pr')
            ->where('pr.nom = :tuteur')
            ->setParameter('tuteur', 'Tuteur')
            ->select('COUNT(DISTINCT p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}