<?php

namespace App\Repository;

use App\Entity\Entreprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;

/**
 * @extends ServiceEntityRepository<Entreprise>
 */
#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class EntrepriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entreprise::class);
    }

    public function findBySearch(string $search): array
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.personnes', 'p')
            ->leftJoin('e.specialites', 's')
            ->leftJoin('e.stages', 'st')
            ->leftJoin('st.etudiant', 'et')
            ->leftJoin('p.profils', 'pr')
            ->leftJoin('e.pays', 'py')
            ->where('e.id LIKE :search')
            ->orWhere('e.rs LIKE :search')
            ->orWhere('e.rue LIKE :search')
            ->orWhere('e.cp LIKE :search')
            ->orWhere('e.ville LIKE :search')
            ->orWhere('e.mail LIKE :search')
            ->orWhere('e.tel LIKE :search')
            ->orWhere('p.nom LIKE :search')
            ->orWhere('p.prenom LIKE :search')
            ->orWhere('p.fonction LIKE :search')
            ->orWhere('p.email LIKE :search')
            ->orWhere('p.tel LIKE :search')
            ->orWhere('s.nom LIKE :search')
            ->orWhere('et.nom LIKE :search')
            ->orWhere('et.prenom LIKE :search')
            ->orWhere('pr.nom LIKE :search AND pr.nom = :jury')
            ->orWhere('py.nom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('jury', 'Jury')
            ->distinct();

        return $qb->getQuery()->getResult();
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
