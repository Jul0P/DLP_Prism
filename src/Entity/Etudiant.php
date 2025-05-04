<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
#[ORM\Table(name: "etudiant")]
class Etudiant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "ETU_ID", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "ETU_NOM", type: "string", length: 50)]
    private ?string $nom = null;

    #[ORM\Column(name: "ETU_PRENOM", type: "string", length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(name: "ETU_EMAIL", type: "string", length: 100, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(name: "ETU_TEL", type: "string", length: 20, nullable: true)]
    private ?string $tel = null;

    #[ORM\OneToMany(mappedBy: "etudiant", targetEntity: Stage::class)]
    private Collection $stages;

    public function __construct()
    {
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
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
            $stage->setEtudiant($this);
        }
        return $this;
    }

    public function removeStage(Stage $stage): self
    {
        if ($this->stages->removeElement($stage)) {
            if ($stage->getEtudiant() === $this) {
                $stage->setEtudiant(null);
            }
        }
        return $this;
    }
}