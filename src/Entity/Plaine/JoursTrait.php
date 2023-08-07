<?php

namespace AcMarche\Edr\Entity\Plaine;

use AcMarche\Edr\Entity\Jour;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait JoursTrait
{
    /**
     * @var Jour[]|Collection
     * */
    private Collection|array $jours = [];

    public function initJours(): void
    {
        $this->jours = new ArrayCollection();
    }

    /**
     * @return Collection|Jour[]
     */
    public function getJours(): Collection
    {
        if (!$this->jours) {
            $this->jours = new ArrayCollection();
        }

        return $this->jours;
    }

    public function addJour(Jour $jour): self
    {
        if (!$this->jours->contains($jour)) {
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
