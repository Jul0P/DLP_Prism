<?php

namespace App\Entity;

use App\Repository\StageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StageRepository::class)]
#[ORM\Table(name: "stage")]
class Stage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "STA_ID", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Personne::class, inversedBy: "stages")]
    #[ORM\JoinColumn(name: "PER_ID", referencedColumnName: "PER_ID", nullable: true, onDelete: "SET NULL")]
    private ?Personne $employe = null;

    #[ORM\ManyToOne(targetEntity: Entreprise::class, inversedBy: "stages")]
    #[ORM\JoinColumn(name: "ENT_ID", referencedColumnName: "ENT_ID", onDelete: "CASCADE")]
    private ?Entreprise $entreprise = null;

    #[ORM\ManyToOne(targetEntity: Etudiant::class, inversedBy: "stages")]
    #[ORM\JoinColumn(name: "ETU_ID", referencedColumnName: "ETU_ID")]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(targetEntity: Specialite::class)]
    #[ORM\JoinColumn(name: "SPE_ID", referencedColumnName: "SPE_ID")]
    private ?Specialite $specialite = null;

    #[ORM\Column(name: "STA_DATEDEBUT", type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: "STA_DATEFIN", type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;
        return $this;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): self
    {
        $this->etudiant = $etudiant;
        return $this;
    }

    public function getSpecialite(): ?Specialite
    {
        return $this->specialite;
    }

    public function setSpecialite(?Specialite $specialite): self
    {
        $this->specialite = $specialite;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getEmploye(): ?Personne
    {
        return $this->employe;
    }

    public function setEmploye(?Personne $employe): self
    {
        $this->employe = $employe;
        return $this;
    }
}