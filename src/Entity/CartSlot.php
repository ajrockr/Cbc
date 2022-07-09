<?php

namespace App\Entity;

use App\Repository\CartSlotRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartSlotRepository::class)]
class CartSlot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $cart_number;

    #[ORM\Column(type: 'string', length: 255)]
    private $cart_row;

    #[ORM\Column(type: 'string', length: 255)]
    private $cart_slot_number;

    #[ORM\Column(type: 'string', length: 255)]
    private $cart_side;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCartNumber(): ?string
    {
        return $this->cart_number;
    }

    public function setCartNumber(string $cart_number): self
    {
        $this->cart_number = $cart_number;

        return $this;
    }

    public function getCartRow(): ?string
    {
        return $this->cart_row;
    }

    public function setCartRow(string $cart_row): self
    {
        $this->cart_row = $cart_row;

        return $this;
    }

    public function getCartSlotNumber(): ?string
    {
        return $this->cart_slot_number;
    }

    public function setCartSlotNumber(string $cart_slot_number): self
    {
        $this->cart_slot_number = $cart_slot_number;

        return $this;
    }

    public function getCartSide(): ?string
    {
        return $this->cart_side;
    }

    public function setCartSide(string $cart_side): self
    {
        $this->cart_side = $cart_side;

        return $this;
    }
}
