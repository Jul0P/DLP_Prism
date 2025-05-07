<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
#[ORM\Table(name: "entreprise")]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "ENT_ID", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "ENT_RS", type: "string", length: 100, nullable: true)]
    private ?string $rs = null;

    #[ORM\Column(name: "ENT_ADRESSE", type: "string", length: 100, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: "ENT_CP", type: "string", length: 5, nullable: true)]
    private ?string $cp = null;

    #[ORM\Column(name: "ENT_VILLE", type: "string", length: 50, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(name: "ENT_TEL", type: "string", length: 20, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(name: "ENT_MAIL", type: "string", length: 100, nullable: true)]
    private ?string $mail = null;

    #[ORM\ManyToOne(targetEntity: Pays::class, inversedBy: "entreprises")]
    #[ORM\JoinColumn(name: "PAY_ID", referencedColumnName: "PAY_ID")]
    private ?Pays $pays = null;

    #[ORM\OneToMany(mappedBy: "entreprise", targetEntity: Personne::class, cascade: ["remove"])]
    private Collection $personnes;

    #[ORM\ManyToMany(targetEntity: Specialite::class, inversedBy: "entreprises")]
    #[ORM\JoinTable(name: "entreprise_specialite")]
    #[ORM\JoinColumn(name: "ENT_ID", referencedColumnName: "ENT_ID")]
    #[ORM\InverseJoinColumn(name: "SPE_ID", referencedColumnName: "SPE_ID")]
    private Collection $specialites;

    #[ORM\OneToMany(mappedBy: "entreprise", targetEntity: Stage::class, cascade: ["remove"])]
    private Collection $stages;

    public function __construct()
    {
        $this->personnes = new ArrayCollection();
        $this->specialites = new ArrayCollection();
        $this->stages = new ArrayCollection();
    }

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

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;
        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): self
    {
        $this->mail = $mail;
        return $this;
    }

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): self
    {
        $this->pays = $pays;
        return $this;
    }

    /**
     * @return Collection<int, Personne>
     */
    public function getPersonnes(): Collection
    {
        return $this->personnes;
    }

    public function addPersonne(Personne $personne): self
    {
        if (!$this->personnes->contains($personne)) {
            $this->personnes[] = $personne;
            $personne->setEntreprise($this);
        }
        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->personnes->removeElement($personne)) {
            if ($personne->getEntreprise() === $this) {
                $personne->setEntreprise(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Specialite>
     */
    public function getSpecialites(): Collection
    {
        return $this->specialites;
    }

    public function addSpecialite(Specialite $specialite): self
    {
        if (!$this->specialites->contains($specialite)) {
            $this->specialites[] = $specialite;
            $specialite->addEntreprise($this);
        }
        return $this;
    }

    public function removeSpecialite(Specialite $specialite): self
    {
        if ($this->specialites->removeElement($specialite)) {
            $specialite->removeEntreprise($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getStages(): Collection
    {
        return $this->stages;
    }

    public function addStage(Stage $stage): self
    {
        if (!$this->stages->contains($stage)) {
            $this->stages[] = $stage;
            $stage->setEntreprise($this);
        }
        return $this;
    }

    public function removeStage(Stage $stage): self
    {
        if ($this->stages->removeElement($stage)) {
            if ($stage->getEntreprise() === $this) {
                $stage->setEntreprise(null);
            }
        }
        return $this;
    }
}