<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrganizationRepository::class)
 */
class Organization
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $branches;

    /**
     * @ORM\ManyToOne(targetEntity=OrganizationType::class, inversedBy="organizations")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?OrganizationType $type;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="organization")
     */
    private Collection $users;

    /**
     * @ORM\Column(type="string", length=7000, nullable=true)
     */
    private ?string $Contact;

    /**
     * @ORM\Column(type="integer", options={"default" : 0}, nullable=true)
     */
    private ?int $sort;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isActive = true;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBranches(): ?bool
    {
        return $this->branches;
    }

    public function setBranches(?bool $branches): self
    {
        $this->branches = $branches;

        return $this;
    }

    public function getType(): ?OrganizationType
    {
        return $this->type;
    }

    public function setType(?OrganizationType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setOrganization($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getOrganization() === $this) {
                $user->setOrganization(null);
            }
        }

        return $this;
    }

    public function __sleep()
    {
        return [];
    }

    public function getContact(): ?string
    {
        return $this->Contact;
    }

    public function setContact(?string $Contact): self
    {
        $this->Contact = $Contact;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
