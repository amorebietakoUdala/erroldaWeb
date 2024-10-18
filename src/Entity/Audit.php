<?php

namespace App\Entity;

use App\Repository\AuditRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuditRepository::class)]
class Audit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $cif;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private $organization;

    #[ORM\Column(type: 'string', length: 255)]
    private $dni;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 1024)]
    private $issuer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCif(): ?string
    {
        return $this->cif;
    }

    public function setCif(string $cif): self
    {
        $this->cif = $cif;

        return $this;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(?string $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(string $dni): self
    {
        $this->dni = $dni;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIssuer(): ?string
    {
        return $this->issuer;
    }

    public function setIssuer(string $issuer): self
    {
        $this->issuer = $issuer;

        return $this;
    }

    public function fill($giltzaUser) 
    {
        $this->setCif(array_key_exists('cif',$giltzaUser)? $giltzaUser['cif']: null);
        $this->setDni(array_key_exists('dni',$giltzaUser)? $giltzaUser['dni']: null);
        $this->setName(array_key_exists('name',$giltzaUser)? $giltzaUser['name']: null);
        $this->setOrganization(array_key_exists('organization',$giltzaUser)? $giltzaUser['organization']: null);
        $this->setIssuer(array_key_exists('issuer',$giltzaUser)? $giltzaUser['issuer']: null);
        return $this;
    }
}

