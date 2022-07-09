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

    #[ORM\ManyToMany(targetEntity: Asset::class, inversedBy: 'repairs')]
    private $assetid;

    #[ORM\ManyToMany(targetEntity: Person::class)]
    private $personid;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private $technicianid;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $modifiedAt;

    #[ORM\ManyToMany(targetEntity: Slot::class)]
    private $cartSlotId;

    #[ORM\Column(type: 'boolean')]
    private $active;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $closedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $notes;

    #[ORM\Column(type: 'array', nullable: true)]
    private $items = [];

    public function __construct()
    {
        $this->assetid = new ArrayCollection();
        $this->personid = new ArrayCollection();
        $this->technicianid = new ArrayCollection();
        $this->cartSlotId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Asset>
     */
    public function getAssetid(): Collection
    {
        return $this->assetid;
    }

    public function addAssetid(Asset $assetid): self
    {
        if (!$this->assetid->contains($assetid)) {
            $this->assetid[] = $assetid;
        }

        return $this;
    }

    public function removeAssetid(Asset $assetid): self
    {
        $this->assetid->removeElement($assetid);

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getPersonid(): Collection
    {
        return $this->personid;
    }

    public function addPersonid(Person $personid): self
    {
        if (!$this->personid->contains($personid)) {
            $this->personid[] = $personid;
        }

        return $this;
    }

    public function removePersonid(Person $personid): self
    {
        $this->personid->removeElement($personid);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getTechnicianid(): Collection
    {
        return $this->technicianid;
    }

    public function addTechnicianid(User $technicianid): self
    {
        if (!$this->technicianid->contains($technicianid)) {
            $this->technicianid[] = $technicianid;
        }

        return $this;
    }

    public function removeTechnicianid(User $technicianid): self
    {
        $this->technicianid->removeElement($technicianid);

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
    public function getCartSlotId(): Collection
    {
        return $this->cartSlotId;
    }

    public function addCartSlotId(Slot $cartSlotId): self
    {
        if (!$this->cartSlotId->contains($cartSlotId)) {
            $this->cartSlotId[] = $cartSlotId;
        }

        return $this;
    }

    public function removeCartSlotId(Slot $cartSlotId): self
    {
        $this->cartSlotId->removeElement($cartSlotId);

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
