<?php

namespace App\Entity;

use App\Repository\ResidentInformationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResidentInformationRepository::class)]
class ResidentInformation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $food = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $care = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $healthRecord = null;

    #[ORM\OneToOne(inversedBy: 'residentInformation')]
    #[ORM\JoinColumn(name: 'id_resident', referencedColumnName: 'id')]
    private ?Residents $resident = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFood(): ?string
    {
        return $this->food;
    }

    public function setFood(?string $food): static
    {
        $this->food = $food;

        return $this;
    }

    public function getCare(): ?string
    {
        return $this->care;
    }

    public function setCare(?string $care): static
    {
        $this->care = $care;

        return $this;
    }

    public function getHealthRecord(): ?string
    {
        return $this->healthRecord;
    }

    public function setHealthRecord(?string $healthRecord): static
    {
        $this->healthRecord = $healthRecord;

        return $this;
    }

    public function getResident(): ?Residents
    {
        return $this->resident;
    }

    public function setResident(?Residents $resident): static
    {
        $this->resident = $resident;

        return $this;
    }
}
