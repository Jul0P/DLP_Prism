<?php

namespace App\Entity;

use App\Repository\SpecialiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialiteRepository::class)]
#[ORM\Table(name: "specialite")]
class Specialite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "SPE_ID", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "SPE_NOM", type: "string", length: 50)]
    private ?string $nom = null;

    #[ORM\ManyToMany(targetEntity: Entreprise::class, mappedBy: "specialites")]
    private Collection $entreprises;

    #[ORM\OneToMany(mappedBy: "specialite", targetEntity: Stage::class)]
    private Collection $stages;

    public function __construct()
    {
        $this->entreprises = new ArrayCollection();
        $this->stages = new ArrayCollection();
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

    /**
     * @return Collection<int, Entreprise>
     */
    public function getEntreprises(): Collection
    {
        return $this->entreprises;
    }

    public function addEntreprise(Entreprise $entreprise): self
    {
        if (!$this->entreprises->contains($entreprise)) {
            $this->entreprises[] = $entreprise;
            $entreprise->addSpecialite($this);
        }
        return $this;
    }

    public function removeEntreprise(Entreprise $entreprise): self
    {
        if ($this->entreprises->removeElement($entreprise)) {
            $entreprise->removeSpecialite($this);
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
            $stage->setSpecialite($this);
        }
        return $this;
    }

    public function removeStage(Stage $stage): self
    {
        if ($this->stages->removeElement($stage)) {
            if ($stage->getSpecialite() === $this) {
                $stage->setSpecialite(null);
            }
        }
        return $this;
    }
}