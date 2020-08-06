<?php

namespace App\Entity;

use App\Repository\JournalRepository;
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
     * @ORM\JoinColumn(nullable=false)
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
}
