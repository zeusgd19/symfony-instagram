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
}
