<?php

namespace App\Entity;

use App\Repository\RepairItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepairItemRepository::class)]
class RepairItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $description;

    #[ORM\Column(type: 'float', nullable: true)]
    private $cost;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $supplier;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $supplierUrl;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $supplierEmail;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $supplierPhone;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(?float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getSupplier(): ?string
    {
        return $this->supplier;
    }

    public function setSupplier(?string $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getSupplierUrl(): ?string
    {
        return $this->supplierUrl;
    }

    public function setSupplierUrl(?string $supplierUrl): self
    {
        $this->supplierUrl = $supplierUrl;

        return $this;
    }

    public function getSupplierEmail(): ?string
    {
        return $this->supplierEmail;
    }

    public function setSupplierEmail(?string $supplierEmail): self
    {
        $this->supplierEmail = $supplierEmail;

        return $this;
    }

    public function getSupplierPhone(): ?string
    {
        return $this->supplierPhone;
    }

    public function setSupplierPhone(?string $supplierPhone): self
    {
        $this->supplierPhone = $supplierPhone;

        return $this;
    }
}
