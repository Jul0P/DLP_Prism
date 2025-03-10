<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
#[ORM\Table(name: "entreprise")]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "ENT_ID", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "ENT_RS", type: "string", length: 50, nullable: true)]
    private ?string $rs = null;

    #[ORM\Column(name: "ENT_ADRESSE", type: "string", length: 32, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: "ENT_CP", type: "string", length: 5, nullable: true)]
    private ?string $cp = null;

    #[ORM\Column(name: "ENT_VILLE", type: "string", length: 32, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(name: "ENT_TUTEUR", type: "string", length: 32, nullable: true)]
    private ?string $tuteur = null;

    #[ORM\Column(name: "ENT_PROFIL", type: "string", length: 32, nullable: true)]
    private ?string $profil = null;

    #[ORM\Column(name: "ENT_ETUDIANT", type: "string", length: 32, nullable: true)]
    private ?string $etudiant = null;

    #[ORM\Column(name: "ENT_JURY", type: "string", length: 32, nullable: true)]
    private ?string $jury = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRs(): ?string
    {
        return $this->rs;
    }

    public function setRs(?string $rs): self
    {
        $this->rs = $rs;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(?string $cp): self
    {
        $this->cp = $cp;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;
        return $this;
    }

    public function getTuteur(): ?string
    {
        return $this->tuteur;
    }

    public function setTuteur(?string $tuteur): self
    {
        $this->tuteur = $tuteur;
        return $this;
    }

    public function getProfil(): ?string
    {
        return $this->profil;
    }

    public function setProfil(?string $profil): self
    {
        $this->profil = $profil;
        return $this;
    }

    public function getEtudiant(): ?string
    {
        return $this->etudiant;
    }

    public function setEtudiant(?string $etudiant): self
    {
        $this->etudiant = $etudiant;
        return $this;
    }

    public function getJury(): ?string
    {
        return $this->jury;
    }

    public function setJury(?string $jury): self
    {
        $this->jury = $jury;
        return $this;
    }
}