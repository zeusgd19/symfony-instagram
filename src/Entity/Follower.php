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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'following')]
    #[ORM\JoinColumn(nullable: false)]
    private User $follower;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'followers')]
    #[ORM\JoinColumn(nullable: false)]
    private User $following;

    // Getters y Setters
    public function getId(): int { return $this->id; }
    public function getFollower(): User { return $this->follower; }
    public function setFollower(User $follower): self { $this->follower = $follower; return $this; }
    public function getFollowing(): User { return $this->following; }
    public function setFollowing(User $following): self { $this->following = $following; return $this; }
}
