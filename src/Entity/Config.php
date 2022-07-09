<?php

namespace App\Entity;

use App\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigRepository::class)]
class Config
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $config_item;

    #[ORM\Column(type: 'string', length: 255)]
    private $config_value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConfigItem(): ?string
    {
        return $this->config_item;
    }

    public function setConfigItem(string $config_item): self
    {
        $this->config_item = $config_item;

        return $this;
    }

    public function getConfigValue(): ?string
    {
        return $this->config_value;
    }

    public function setConfigValue(string $config_value): self
    {
        $this->config_value = $config_value;

        return $this;
    }
}
