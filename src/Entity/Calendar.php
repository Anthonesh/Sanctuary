<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\CalendarRepository')]
class Calendar
{
    #[ORM\Id, 
    ORM\GeneratedValue, 
    ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $places = null;

    #[ORM\OneToMany(targetEntity: Volunteer::class, mappedBy: 'calendar')]
    private Collection $volunteers;

    #[ORM\OneToMany(targetEntity: ReservationForm::class, mappedBy: 'calendar')]
    private Collection $reservation_form;

    #[ORM\Column(nullable: true)]
    private ?int $volunteer_places = null;

    public function __construct()
    {
        $this->volunteers = new ArrayCollection();
        $this->reservation_form = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPlaces(): ?int
    {
        return $this->places;
    }

    public function setPlaces(?int $places): static
    {
        if ($places < 0) {
            throw new \Exception("Le nombre de places disponibles ne peut pas être négatif.");
        }
        $this->places = $places;

        return $this;
    }

    public function getVolunteerPlaces(): ?int
    {
        return $this->volunteer_places;
    }

    public function setVolunteerPlaces(?int $volunteer_places): static
    {
        if ($volunteer_places < 0) {
            throw new \Exception("Le nombre de places disponibles ne peut pas être négatif.");
        }
        $this->volunteer_places = $volunteer_places;  // Assurez-vous d'utiliser volunteer_places ici
    
        return $this;
    }

    /**
     * @return Collection<int, Volunteer>
     */
    public function getVolunteers(): Collection
    {
        return $this->volunteers;
    }

    public function addVolunteer(Volunteer $volunteer): static
    {
        if (!$this->volunteers->contains($volunteer)) {
            $this->volunteers->add($volunteer);
            $volunteer->setCalendar($this);
        }

        return $this;
    }

    public function removeVolunteer(Volunteer $volunteer): static
    {
        if ($this->volunteers->removeElement($volunteer)) {
            // set the owning side to null (unless already changed)
            if ($volunteer->getCalendar() === $this) {
                $volunteer->setCalendar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReservationForm>
     */
    public function getForms(): Collection
    {
        return $this->reservation_form;
    }

    public function addForm(ReservationForm $form): static
    {
        if (!$this->reservation_form->contains($form)) {
            $this->reservation_form->add($form);
            $form->setCalendar($this);
        }

        return $this;
    }

    public function removeForm(ReservationForm $form): static
    {
        if ($this->reservation_form->removeElement($form)) {
            // set the owning side to null (unless already changed)
            if ($form->getCalendar() === $this) {
                $form->setCalendar(null);
            }
        }

        return $this;
    }


}


