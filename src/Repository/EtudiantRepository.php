<?php

namespace App\Repository;

use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
class EtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiant::class);
    }

    public function findBySearch(string $search): array
    {
        $qb = $this->createQueryBuilder('et')
            ->leftJoin('et.stages', 'st')
            ->leftJoin('st.entreprise', 'e')
            ->where('et.id LIKE :search')
            ->orWhere('et.nom LIKE :search')
            ->orWhere('et.prenom LIKE :search')
            ->orWhere('et.email LIKE :search')
            ->orWhere('et.tel LIKE :search')
            ->orWhere('e.rs LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->distinct();

        return $qb->getQuery()->getResult();
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('et')
            ->select('COUNT(et.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}