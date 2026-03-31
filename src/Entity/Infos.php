<?php

namespace App\Entity;

use App\Repository\InfosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InfosRepository::class)]
class Infos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $rang = null;

    #[ORM\Column(length: 255)]
    private ?string $victoire = null;

    #[ORM\Column(length: 255)]
    private ?string $defaite = null;

    // --- LA RELATION ONE-TO-ONE (On garde uniquement celle-ci) ---
    #[ORM\OneToOne(inversedBy: 'infos', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRang(): ?string
    {
        return $this->rang;
    }

    public function setRang(string $rang): static
    {
        $this->rang = $rang;

        return $this;
    }

    public function getVictoire(): ?string
    {
        return $this->victoire;
    }

    public function setVictoire(string $victoire): static
    {
        $this->victoire = $victoire;

        return $this;
    }

    public function getDefaite(): ?string
    {
        return $this->defaite;
    }

    public function setDefaite(string $defaite): static
    {
        $this->defaite = $defaite;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    // Astuce pour que le formulaire de création d'Infos soit joli
    public function __toString(): string
    {
        return $this->user ? $this->user->getUsername() : 'Sans utilisateur';
    }
}
