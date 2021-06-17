<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="usr")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private ?string $username;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string|null The hashed password
     * @ORM\Column(type="string")
     */
    private ?string $password;

    /**
     * @ORM\ManyToOne(targetEntity=Organization::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Organization $organization;

    /**
     * @ORM\OneToMany(targetEntity=UserAlert::class, mappedBy="usr", orphanRemoval=true)
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

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
            $userAlert->setUsr($this);
        }

        return $this;
    }

    public function removeUserAlert(UserAlert $userAlert): self
    {
        if ($this->userAlerts->contains($userAlert)) {
            $this->userAlerts->removeElement($userAlert);
            // set the owning side to null (unless already changed)
            if ($userAlert->getUsr() === $this) {
                $userAlert->setUsr(null);
            }
        }

        return $this;
    }
}
