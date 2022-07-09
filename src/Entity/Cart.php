<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $cart_number;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $cart_description;

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

    public function getCartDescription(): ?string
    {
        return $this->cart_description;
    }

    public function setCartDescription(?string $cart_description): self
    {
        $this->cart_description = $cart_description;

        return $this;
    }
}
