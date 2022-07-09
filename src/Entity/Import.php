<?php

namespace App\Entity;

use App\Repository\ImportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImportRepository::class)]
class Import
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $importedBy;

    #[ORM\Column(type: 'datetime_immutable')]
    private $importedAt;

    #[ORM\Column(type: 'array')]
    private $importedData = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImportedBy(): ?User
    {
        return $this->importedBy;
    }

    public function setImportedBy(?User $importedBy): self
    {
        $this->importedBy = $importedBy;

        return $this;
    }

    public function getImportedAt(): ?\DateTimeImmutable
    {
        return $this->importedAt;
    }

    public function setImportedAt(\DateTimeImmutable $importedAt): self
    {
        $this->importedAt = $importedAt;

        return $this;
    }

    public function getImportedData(): ?array
    {
        return $this->importedData;
    }

    public function setImportedData(array $importedData): self
    {
        $this->importedData = $importedData;

        return $this;
    }
}
