<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    public const STATUS_EN_ATTENTE = 'en_attente';
    public const STATUS_CONFIRME  = 'confirme';
    public const STATUS_ANNULE    = 'annule';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $nbCouvert = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'string', length: 5)]
    private ?string $heure = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $allergies = null;

    #[ORM\Column]
    private ?bool $allergiesActive = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_EN_ATTENTE;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "reservations")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbCouvert(): ?int
    {
        return $this->nbCouvert;
    }

    public function setNbCouvert(int $nbCouvert): static
    {
        $this->nbCouvert = $nbCouvert;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getHeure(): ?string
    {
        return $this->heure;
    }

    public function setHeure(?string $heure): static
    {
        $this->heure = $heure;
        return $this;
    }

    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    public function setAllergies(?string $allergies): static
    {
        $this->allergies = $allergies;
        return $this;
    }

    public function isAllergiesActive(): ?bool
    {
        return $this->allergiesActive;
    }

    public function setAllergiesActive(?bool $allergiesActive): self
    {
        $this->allergiesActive = $allergiesActive;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, [self::STATUS_EN_ATTENTE, self::STATUS_CONFIRME, self::STATUS_ANNULE])) {
            throw new \InvalidArgumentException("Statut invalide");
        }
        $this->status = $status;
        return $this;
    }

    public function getUser(): ?Utilisateur
    {
        return $this->user;
    }

    public function setUser(?Utilisateur $user): self
    {
        $this->user = $user;
        return $this;
    }
}