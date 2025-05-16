<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findBySearch(string $search): array
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.id LIKE :search')
            ->orWhere('u.nom LIKE :search')
            ->orWhere('u.prenom LIKE :search')
            ->orWhere('u.email LIKE :search')
            ->orWhere('u.roles LIKE :search')
            ->setParameter('search', '%' . $search . '%');

        return $qb->getQuery()->getResult();
    }
}