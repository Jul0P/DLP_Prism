<?php

namespace App\Repository;

use App\Entity\Stage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StageRepository::class)]
class StageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stage::class);
    }

    public function findBySearch(string $search): array
    {
        $qb = $this->createQueryBuilder('st')
            ->leftJoin('st.etudiant', 'et')
            ->leftJoin('st.entreprise', 'e')
            ->leftJoin('st.specialite', 's')
            ->leftJoin('st.employe', 'p')
            ->where('st.id LIKE :search')
            ->orWhere('st.dateDebut LIKE :search')
            ->orWhere('st.dateFin LIKE :search')
            ->orWhere('et.nom LIKE :search')
            ->orWhere('et.prenom LIKE :search')
            ->orWhere('e.rs LIKE :search')
            ->orWhere('s.nom LIKE :search')
            ->orWhere('p.nom LIKE :search')
            ->orWhere('p.prenom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->distinct();

        return $qb->getQuery()->getResult();
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('st')
            ->select('COUNT(st.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countActive(): int
    {
        return (int) $this->createQueryBuilder('st')
            ->where('st.dateFin >= :today AND st.dateDebut <= :today')
            ->setParameter('today', new \DateTime())
            ->select('COUNT(st.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}