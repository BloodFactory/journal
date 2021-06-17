<?php

namespace App\Entity;

use App\Repository\UserAlertRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserAlertRepository::class)
 */
class UserAlert
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userAlerts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usr;

    /**
     * @ORM\ManyToOne(targetEntity=Alert::class, inversedBy="userAlerts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $alert;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

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

    public function getAlert(): ?Alert
    {
        return $this->alert;
    }

    public function setAlert(?Alert $alert): self
    {
        $this->alert = $alert;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
