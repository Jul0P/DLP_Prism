<?php

namespace App\Repository;

use App\Entity\Pays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialiteRepository::class)]
class SpecialiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pays::class);
    }
}