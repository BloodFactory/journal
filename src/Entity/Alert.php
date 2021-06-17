<?php

namespace App\Entity;

use App\Repository\AlertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AlertRepository::class)
 */
class Alert
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10000)
     */
    private $message;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $beginAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $once;

    /**
     * @ORM\OneToMany(targetEntity=UserAlert::class, mappedBy="alert", orphanRemoval=true)
     */
    private $userAlerts;

    public function __construct()
    {
        $this->userAlerts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getBeginAt(): ?\DateTimeInterface
    {
        return $this->beginAt;
    }

    public function setBeginAt(?\DateTimeInterface $beginAt): self
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getOnce(): ?bool
    {
        return $this->once;
    }

    public function setOnce(bool $once): self
    {
        $this->once = $once;

        return $this;
    }

    /**
     * @return Collection|UserAlert[]
     */
    public function getUserAlerts(): Collection
    {
        return $this->userAlerts;
    }

    public function addUserAlert(UserAlert $userAlert): self
    {
        if (!$this->userAlerts->contains($userAlert)) {
            $this->userAlerts[] = $userAlert;
            $userAlert->setAlert($this);
        }

        return $this;
    }

    public function removeUserAlert(UserAlert $userAlert): self
    {
        if ($this->userAlerts->contains($userAlert)) {
            $this->userAlerts->removeElement($userAlert);
            // set the owning side to null (unless already changed)
            if ($userAlert->getAlert() === $this) {
                $userAlert->setAlert(null);
            }
        }

        return $this;
    }
}
