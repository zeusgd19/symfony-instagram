<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\ManyToMany(targetEntity: UserPostgres::class, mappedBy: 'likedPosts')]
    private Collection $likedBy;

    #[ORM\ManyToMany(targetEntity: UserPostgres::class, mappedBy: 'savedPosts')]
    private Collection $savedBy;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    public function __construct()
    {
        $this->likedBy = new ArrayCollection();
        $this->savedBy = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

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

    /**
     * @return Collection<int, UserPostgres>
     */
    public function getLikedBy(): Collection
    {
        return $this->likedBy;
    }

    public function addLikedBy(UserPostgres $likedBy): static
    {
        if (!$this->likedBy->contains($likedBy)) {
            $this->likedBy->add($likedBy);
        }

        return $this;
    }

    public function removeLikedBy(UserPostgres $likedBy): static
    {
        $this->likedBy->removeElement($likedBy);

        return $this;
    }

    /**
     * @return Collection<int, UserPostgres>
     */
    public function getSavedBy(): Collection
    {
        return $this->savedBy;
    }

    public function addSavedBy(UserPostgres $savedBy): static
    {
        if (!$this->savedBy->contains($savedBy)) {
            $this->savedBy->add($savedBy);
        }

        return $this;
    }

    public function removeSavedBy(UserPostgres $savedBy): static
    {
        $this->savedBy->removeElement($savedBy);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}

