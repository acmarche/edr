<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Presence\Presence;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait PresencesTuteurTrait
{
    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Presence[]|Collection
     */
    #[ORM\OneToMany(targetEntity: Presence::class, mappedBy: 'tuteur', cascade: ['remove'])]
    private Collection $presences;

    /**
     * @return Collection|Presence[]
     */
    public function getPresences(): Collection
    {
        return $this->presences;
    }

    public function addPresence(Presence $presence): self
    {
        if (! $this->presences->contains($presence)) {
            $this->presences[] = $presence;
            $presence->setTuteur($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ($this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
            // set the owning side to null (unless already changed)
            if ($presence->getTuteur() === $this) {
                $presence->setTuteur(null);
            }
        }

        return $this;
    }
}
