<?php

namespace App\Entity;

use App\Repository\JournalRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JournalRepository::class)
 */
class Journal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Organization::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Organization $organization;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $total = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $atWork = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $onHoliday = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $remoteTotal = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $remotePregnant = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $remoteWithChildren = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $remoteOver60 = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $onTwoWeekQuarantine = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $onSickLeave = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $sickCOVID = 0;

    /**
     * @ORM\Column(type="string", length=4000, nullable=true)
     */
    private ?string $note = '';

    /**
     * @ORM\ManyToOne(targetEntity=Journal::class, inversedBy="branches")
     */
    private ?Journal $headOffice;

    /**
     * @ORM\OneToMany(targetEntity=Journal::class, mappedBy="headOffice", fetch="EAGER")
     */
    private Collection $branches;

    /**
     * @ORM\Column(type="date")
     */
    private ?DateTimeInterface $date;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isActive = true;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $shift_rest;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Die;

    public function __construct()
    {
        $this->branches = new ArrayCollection();
        $this->date = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getAtWork(): ?int
    {
        return $this->atWork;
    }

    public function setAtWork(int $atWork): self
    {
        $this->atWork = $atWork;

        return $this;
    }

    public function getOnHoliday(): ?int
    {
        return $this->onHoliday;
    }

    public function setOnHoliday(int $onHoliday): self
    {
        $this->onHoliday = $onHoliday;

        return $this;
    }

    public function getRemoteTotal(): ?int
    {
        return $this->remoteTotal;
    }

    public function setRemoteTotal(int $remoteTotal): self
    {
        $this->remoteTotal = $remoteTotal;

        return $this;
    }

    public function getRemotePregnant(): ?int
    {
        return $this->remotePregnant;
    }

    public function setRemotePregnant(int $remotePregnant): self
    {
        $this->remotePregnant = $remotePregnant;

        return $this;
    }

    public function getRemoteWithChildren(): ?int
    {
        return $this->remoteWithChildren;
    }

    public function setRemoteWithChildren(int $remoteWithChildren): self
    {
        $this->remoteWithChildren = $remoteWithChildren;

        return $this;
    }

    public function getRemoteOver60(): ?int
    {
        return $this->remoteOver60;
    }

    public function setRemoteOver60(int $remoteOver60): self
    {
        $this->remoteOver60 = $remoteOver60;

        return $this;
    }

    public function getOnTwoWeekQuarantine(): ?int
    {
        return $this->onTwoWeekQuarantine;
    }

    public function setOnTwoWeekQuarantine(int $onTwoWeekQuarantine): self
    {
        $this->onTwoWeekQuarantine = $onTwoWeekQuarantine;

        return $this;
    }

    public function getOnSickLeave(): ?int
    {
        return $this->onSickLeave;
    }

    public function setOnSickLeave(int $onSickLeave): self
    {
        $this->onSickLeave = $onSickLeave;

        return $this;
    }

    public function getSickCOVID(): ?int
    {
        return $this->sickCOVID;
    }

    public function setSickCOVID(int $sickCOVID): self
    {
        $this->sickCOVID = $sickCOVID;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getHeadOffice(): ?self
    {
        return $this->headOffice;
    }

    public function setHeadOffice(?self $headOffice): self
    {
        $this->headOffice = $headOffice;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getBranches(): Collection
    {
        return $this->branches;
    }

    public function addBranch(self $branch): self
    {
        if (!$this->branches->contains($branch)) {
            $this->branches[] = $branch;
            $branch->setHeadOffice($this);
        }

        return $this;
    }

    public function removeBranch(self $branch): self
    {
        if ($this->branches->contains($branch)) {
            $this->branches->removeElement($branch);
            // set the owning side to null (unless already changed)
            if ($branch->getHeadOffice() === $this) {
                $branch->setHeadOffice(null);
            }
        }

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getShiftRest(): ?int
    {
        return $this->shift_rest;
    }

    public function setShiftRest(?int $shift_rest): self
    {
        $this->shift_rest = $shift_rest;

        return $this;
    }

    public function getDie(): ?int
    {
        return $this->Die;
    }

    public function setDie(?int $Die): self
    {
        $this->Die = $Die;

        return $this;
    }

}
