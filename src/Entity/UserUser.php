<?php

namespace App\Entity;

use App\Repository\UserUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserUserRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_user_relation',  columns:["user_source_id", "user_target_id"])]

class UserUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\ManyToOne]
    private ?User $user_source = null;

    #[ORM\ManyToOne]
    private ?User $user_target = null;

    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUserTarget(): ?User
    {
        return $this->user_target;
    }

    public function setUserTarget(?User $user_target): static
    {
        $this->user_target = $user_target;

        return $this;
    }

    public function getUserSource(): ?User
    {
        return $this->user_source;
    }

    public function setUserSource(?User $user_source): static
    {
        $this->user_source = $user_source;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->Etat;
    }

    public function setEtat(?string $Etat): static
    {
        $this->Etat = $Etat;

        return $this;
    }
}
