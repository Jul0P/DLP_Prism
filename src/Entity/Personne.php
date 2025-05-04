<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
#[ORM\Table(name: "personne")]
class Personne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "PER_ID", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "PER_NOM", type: "string", length: 50)]
    private ?string $nom = null;

    #[ORM\Column(name: "PER_PRENOM", type: "string", length: 50, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(name: "PER_FONCTION", type: "string", length: 100, nullable: true)]
    private ?string $fonction = null;

    #[ORM\Column(name: "PER_EMAIL", type: "string", length: 100, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(name: "PER_TEL", type: "string", length: 20, nullable: true)]
    private ?string $tel = null;

    #[ORM\ManyToOne(targetEntity: Entreprise::class, inversedBy: "personnes")]
    #[ORM\JoinColumn(name: "ENT_ID", referencedColumnName: "ENT_ID")]
    private ?Entreprise $entreprise = null;

    #[ORM\ManyToMany(targetEntity: Profil::class, inversedBy: "personnes")]
    #[ORM\JoinTable(name: "personne_profil")]
    #[ORM\JoinColumn(name: "PER_ID", referencedColumnName: "PER_ID")]
    #[ORM\InverseJoinColumn(name: "PRO_ID", referencedColumnName: "PRO_ID")]
    private Collection $profils;

    public function __construct()
    {
        $this->profils = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): self
    {
        $this->fonction = $fonction;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;
        return $this;
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

    /**
     * @return Collection<int, Profil>
     */
    public function getProfils(): Collection
    {
        return $this->profils;
    }

    public function addProfil(Profil $profil): self
    {
        if (!$this->profils->contains($profil)) {
            $this->profils[] = $profil;
            $profil->addPersonne($this);
        }
        return $this;
    }

    public function removeProfil(Profil $profil): self
    {
        if ($this->profils->removeElement($profil)) {
            $profil->removePersonne($this);
        }
        return $this;
    }
}