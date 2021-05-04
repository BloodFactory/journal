<?php

namespace App\Entity\Log;

use App\Entity\User;
use App\Repository\Log\RequestLogRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RequestLogRepository::class)
 */
class RequestLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private ?User $usr;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $moment;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $content = [];

    /**
     * @ORM\Column(type="string", length=15)
     */
    private ?string $method;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $path;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $query = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $request = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsr(): ?User
    {
        return $this->usr;
    }

    public function setUsr(?User $usr): self
    {
        $this->usr = $usr;

        return $this;
    }

    public function getMoment(): ?DateTimeInterface
    {
        return $this->moment;
    }

    public function setMoment(DateTimeInterface $moment): self
    {
        $this->moment = $moment;

        return $this;
    }

    public function getContent(): ?array
    {
        return $this->content;
    }

    public function setContent(?array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getQuery(): ?array
    {
        return $this->query;
    }

    public function setQuery(?array $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getRequest(): ?array
    {
        return $this->request;
    }

    public function setRequest(?array $request): self
    {
        $this->request = $request;

        return $this;
    }
}
