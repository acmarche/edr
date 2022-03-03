<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Jour;
use Doctrine\Common\Collections\Collection;

trait JoursTrait
{
    /**
     * @var Jour[]|Collection
     */
    private Collection $jours;

    /**
     * @return Collection|Jour[]
     */
    public function getJours(): Collection
    {
        return $this->jours;
    }

    public function addJour(Jour $jour): self
    {
        if (! $this->jours->contains($jour)) {
            $this->jours[] = $jour;
        }

        return $this;
    }

    public function removeJour(Jour $jour): self
    {
        if ($this->jours->contains($jour)) {
            $this->jours->removeElement($jour);
        }

        return $this;
    }
}
