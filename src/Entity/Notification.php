<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserPostgres $notifiedUser = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserPostgres $generatedNotifyBy = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contentComment = null;

    #[ORM\ManyToOne]
    private ?Post $post = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expireDate = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->expireDate = (new \DateTimeImmutable())->modify('+1 day');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getNotifiedUser(): ?UserPostgres
    {
        return $this->notifiedUser;
    }

    public function setNotifiedUser(?UserPostgres $notifiedUser): static
    {
        $this->notifiedUser = $notifiedUser;

        return $this;
    }

    public function getGeneratedNotifyBy(): ?UserPostgres
    {
        return $this->generatedNotifyBy;
    }

    public function setGeneratedNotifyBy(?UserPostgres $generatedNotifyBy): static
    {
        $this->generatedNotifyBy = $generatedNotifyBy;

        return $this;
    }

    public function getContentComment(): ?string
    {
        return $this->contentComment;
    }

    public function setContentComment(?string $contentComment): static
    {
        $this->contentComment = $contentComment;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

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

    public function getExpireDate(): ?\DateTimeImmutable
    {
        return $this->expireDate;
    }

    public function setExpireDate(\DateTimeImmutable $expireDate): static
    {
        $this->expireDate = $expireDate;

        return $this;
    }
}
