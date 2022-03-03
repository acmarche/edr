<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Tuteur;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait TuteursTrait
{
    /**
     * @var Tuteur[]|Collection
     */
    #[ORM\ManyToMany(targetEntity: Tuteur::class, inversedBy: 'users')]
    private Collection $tuteurs;

    /**
     * @return Collection|Tuteur[]
     */
    public function getTuteurs(): Collection
    {
        return $this->tuteurs;
    }

    public function addTuteur(Tuteur $tuteur): self
    {
        if (! $this->tuteurs->contains($tuteur)) {
            $this->tuteurs[] = $tuteur;
        }

        return $this;
    }

    public function removeTuteur(Tuteur $tuteur): self
    {
        if ($this->tuteurs->contains($tuteur)) {
            $this->tuteurs->removeElement($tuteur);
        }

        return $this;
    }
}
