<?php

namespace AcMarche\Edr\Facture\Dto;

use AcMarche\Edr\Entity\Presence\Presence;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class FactureDto
{
    /**
     * @var Presence[]
     */
    private Collection|array $presences = [];

    public function __construct(array $presences)
    {
        $this->presences = new ArrayCollection($presences);
    }

    /**
     * @return Collection|Presence[]
     */
    public function getPresences(): Collection
    {
        return $this->presences;
    }

    public function addPresence(Presence $presence): self
    {
        if ([] === $this->presences->contains($presence)) {
            $this->presences[] = $presence;
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ([] !== $this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
        }

        return $this;
    }
}
