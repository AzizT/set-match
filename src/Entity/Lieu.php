<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LieuRepository::class)
 */
class Lieu
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Planning::class, mappedBy="lieu")
     */
    private $planning;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $city;

    public function __construct()
    {
        $this->planning = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Planning[]
     */
    public function getPlanning(): Collection
    {
        return $this->planning;
    }

    public function addPlanning(Planning $planning): self
    {
        if (!$this->planning->contains($planning)) {
            $this->planning[] = $planning;
            $planning->setLieu($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        if ($this->planning->contains($planning)) {
            $this->planning->removeElement($planning);
            // set the owning side to null (unless already changed)
            if ($planning->getLieu() === $this) {
                $planning->setLieu(null);
            }
        }

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }
}
