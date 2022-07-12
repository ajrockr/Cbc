<?php

namespace App\Entity;

use App\Repository\RepairRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepairRepository::class)]
class Repair
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $assetid;

    #[ORM\Column(type: 'integer')]
    private $personid;

    #[ORM\Column(type: 'integer')]
    private $technicianid;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $modifiedAt;

    #[ORM\Column(type: 'integer')]
    private $cartSlotId;

    #[ORM\Column(type: 'boolean')]
    private $active;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $closedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $notes;

    #[ORM\Column(type: 'array', nullable: true)]
    private $items = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssetid(): ?int
    {
        return $this->assetid;
    }

    public function setAssetid(int $assetid): self
    {
        $this->assetid = $assetid;

        return $this;
    }

    public function getPersonid(): ?int
    {
        return $this->personid;
    }

    public function setPersonid(int $personid): self
    {
        $this->personid = $personid;

        return $this;
    }

    public function getTechnicianid(): ?int
    {
        return $this->technicianid;
    }

    public function setTechnicianid(int $technicianid): self
    {
        $this->technicianid = $technicianid;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(\DateTimeImmutable $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return Collection<int, Slot>
     */
    public function getCartSlotId(): ?int
    {
        return $this->cartSlotId;
    }

    public function setCartSlotId(int $cartSlotId): self
    {
        $this->cartSlotId = $cartSlotId;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function setItems(?array $items): self
    {
        $this->items = $items;

        return $this;
    }
}
