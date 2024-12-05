<?php

namespace App\Entity;

use App\Repository\StoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoryRepository::class)]
class Story
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserPostgres $userStory = null;

    #[ORM\Column(length: 255)]
    private ?string $imageStory = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $expireDate = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->expireDate = (new \DateTimeImmutable())->modify('+1 day');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserStory(): ?UserPostgres
    {
        return $this->userStory;
    }

    public function setUserStory(?UserPostgres $userStory): static
    {
        $this->userStory = $userStory;

        return $this;
    }

    public function getImageStory(): ?string
    {
        return $this->imageStory;
    }

    public function setImageStory(string $imageStory): static
    {
        $this->imageStory = $imageStory;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpireDate(): ?\DateTimeInterface
    {
        return $this->expireDate;
    }

    public function setExpireDate(\DateTimeInterface $expireDate): static
    {
        $this->expireDate = $expireDate;

        return $this;
    }
}
