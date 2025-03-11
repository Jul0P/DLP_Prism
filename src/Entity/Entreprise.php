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

    #[ORM\Column(name: "ENT_ADRESSE", type: "string", length: 100, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: "ENT_CP", type: "string", length: 5, nullable: true)]
    private ?string $cp = null;

    #[ORM\Column(name: "ENT_VILLE", type: "string", length: 50, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(name: "ENT_TEL", type: "string", length: 20, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(name: "ENT_MAIL", type: "string", length: 50, nullable: true)]
    private ?string $mail = null;

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
}