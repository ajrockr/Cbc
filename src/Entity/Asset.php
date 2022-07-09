<?php

namespace App\Entity;

use App\Repository\AssetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssetRepository::class)]
class Asset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $asset_tag;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $serial_number;

    #[ORM\Column(type: 'boolean')]
    private $NeedsRepair;

    #[ORM\ManyToMany(targetEntity: Repair::class, mappedBy: 'assetid', fetch: 'EXTRA_LAZY')]
    private $repairs;

    public function __construct()
    {
        $this->repairs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssetTag(): ?string
    {
        return $this->asset_tag;
    }

    public function setAssetTag(string $asset_tag): self
    {
        $this->asset_tag = $asset_tag;

        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serial_number;
    }

    public function setSerialNumber(?string $serial_number): self
    {
        $this->serial_number = $serial_number;

        return $this;
    }

    public function isNeedsRepair(): ?bool
    {
        return $this->NeedsRepair;
    }

    public function setNeedsRepair(bool $NeedsRepair): self
    {
        $this->NeedsRepair = $NeedsRepair;

        return $this;
    }

    /**
     * @return Collection<int, Repair>
     */
    public function getRepairs(): Collection
    {
        return $this->repairs;
    }

    public function addRepair(Repair $repair): self
    {
        if (!$this->repairs->contains($repair)) {
            $this->repairs[] = $repair;
            $repair->addAssetid($this);
        }

        return $this;
    }

    public function removeRepair(Repair $repair): self
    {
        if ($this->repairs->removeElement($repair)) {
            $repair->removeAssetid($this);
        }

        return $this;
    }
}
