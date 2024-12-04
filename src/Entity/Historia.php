<?php

namespace App\Entity;

use App\Repository\HistoriaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriaRepository::class)]
class Historia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'historias')]
    private ?UserPostgres $usuario = null;

    #[ORM\Column(length: 255)]
    private ?string $contenido = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaPublicacion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaExpiracion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?UserPostgres
    {
        return $this->usuario;
    }

    public function setUsuario(?UserPostgres $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    public function setContenido(string $contenido): static
    {
        $this->contenido = $contenido;

        return $this;
    }

    public function getFechaPublicacion(): ?\DateTimeInterface
    {
        return $this->fechaPublicacion;
    }

    public function setFechaPublicacion(\DateTimeInterface $fechaPublicacion): static
    {
        $this->fechaPublicacion = $fechaPublicacion;

        return $this;
    }

    public function getFechaExpiracion(): ?\DateTimeInterface
    {
        return $this->fechaExpiracion;
    }

    public function setFechaExpiracion(\DateTimeInterface $fechaExpiracion): static
    {
        $this->fechaExpiracion = $fechaExpiracion;

        return $this;
    }
}
