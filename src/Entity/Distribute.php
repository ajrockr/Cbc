<?php

namespace App\Entity;

use App\Repository\DistributeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DistributeRepository::class)]
class Distribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $assetTag;

    #[ORM\Column(type: 'string', length: 255)]
    private $slotNumber;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'boolean')]
    private $distributed;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $distributedAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $distributedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssetTag(): ?string
    {
        return $this->assetTag;
    }

    public function setAssetTag(string $assetTag): self
    {
        $this->assetTag = $assetTag;

        return $this;
    }

    public function getSlotNumber(): ?string
    {
        return $this->slotNumber;
    }

    public function setSlotNumber(string $slotNumber): self
    {
        $this->slotNumber = $slotNumber;

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

    public function isDistributed(): ?bool
    {
        return $this->distributed;
    }

    public function setDistributed(bool $distributed): self
    {
        $this->distributed = $distributed;

        return $this;
    }

    public function getDistributedAt(): ?\DateTimeImmutable
    {
        return $this->distributedAt;
    }

    public function setDistributedAt(\DateTimeImmutable $distributedAt): self
    {
        $this->distributedAt = $distributedAt;

        return $this;
    }

    public function getDistributedBy(): ?string
    {
        return $this->distributedBy;
    }

    public function setDistributedBy(string $distributedBy): self
    {
        $this->distributedBy = $distributedBy;

        return $this;
    }
}
