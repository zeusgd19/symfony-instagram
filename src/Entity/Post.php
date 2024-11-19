<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: 'post')]  // Especificar el nombre de la tabla como 'post' (minúsculas)
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    #[ORM\Column(length: 255, nullable: true, options: ["default" => " "])]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: UserPostgres::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]  // Especifica el nombre de la columna 'user_id'
    private ?UserPostgres $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self  // Permitido null
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?UserPostgres
    {
        return $this->user;
    }

    public function setUser(?UserPostgres $user): self
    {
        $this->user = $user;

        return $this;
    }
}
