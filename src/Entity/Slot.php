<?php

namespace App\Entity;

use App\Repository\SlotRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlotRepository::class)]
class Slot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: CartSlot::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $number;

    #[ORM\OneToOne(targetEntity: Person::class, cascade: ['persist', 'remove'])]
    private $assignedPersonId;

    #[ORM\OneToOne(targetEntity: Asset::class, cascade: ['persist', 'remove'])]
    private $assignedAssetId;

    #[ORM\Column(type: 'boolean')]
    private $IsFinished;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?CartSlot
    {
        return $this->number;
    }

    public function setNumber(CartSlot $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getAssignedPersonId(): ?Person
    {
        return $this->assignedPersonId;
    }

    public function setAssignedPersonId(?Person $assignedPersonId): self
    {
        $this->assignedPersonId = $assignedPersonId;

        return $this;
    }

    public function getAssignedAssetId(): ?Asset
    {
        return $this->assignedAssetId;
    }

    public function setAssignedAssetId(?Asset $assignedAssetId): self
    {
        $this->assignedAssetId = $assignedAssetId;

        return $this;
    }

    public function isIsFinished(): ?bool
    {
        return $this->IsFinished;
    }

    public function setIsFinished(bool $IsFinished): self
    {
        $this->IsFinished = $IsFinished;

        return $this;
    }
}
