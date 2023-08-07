<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Presence\Presence;
use Doctrine\Common\Collections\Collection;

trait PresencesTrait
{
    private Collection|array $presences = [];

    /**
     * @return Collection|Presence[]
     */
    public function getPresences(): Collection
    {
        return $this->presences;
    }

    public function addPresence(Presence $presence): self
    {
        if (!$this->presences->contains($presence)) {
            $this->presences[] = $presence;
            $presence->setEnfant($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ($this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
            // set the owning side to null (unless already changed)
            if ($presence->getEnfant() === $this) {
                $presence->setEnfant(null);
            }
        }

        return $this;
    }
}
