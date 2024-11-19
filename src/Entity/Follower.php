<?php

// src/Entity/Follower.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'follower')]
class Follower
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: UserPostgres::class, inversedBy: 'following')]
    #[ORM\JoinColumn(nullable: false)]
    private UserPostgres $follower;

    #[ORM\ManyToOne(targetEntity: UserPostgres::class, inversedBy: 'followers')]
    #[ORM\JoinColumn(nullable: false)]
    private UserPostgres $following;

    // Getters y Setters
    public function getId(): int { return $this->id; }
    public function getFollower(): UserPostgres { return $this->follower; }
    public function setFollower(UserPostgres $follower): self { $this->follower = $follower; return $this; }
    public function getFollowing(): UserPostgres { return $this->following; }
    public function setFollowing(UserPostgres $following): self { $this->following = $following; return $this; }
}
