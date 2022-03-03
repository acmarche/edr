<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Presence\Accueil;
use Doctrine\Common\Collections\Collection;

trait AccueilsTrait
{
    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Accueil[]|Collection
     */
    private Collection $accueils;

    /**
     * @return Collection|Accueil[]
     */
    public function getAccueils(): Collection
    {
        return $this->accueils;
    }

    public function addAccueil(Accueil $accueil): self
    {
        if (! $this->accueils->contains($accueil)) {
            $this->accueils[] = $accueil;
            $accueil->setEnfant($this);
        }

        return $this;
    }

    public function removeAccueil(Accueil $accueil): self
    {
        if ($this->accueils->contains($accueil)) {
            $this->accueils->removeElement($accueil);
            // set the owning side to null (unless already changed)
            if ($accueil->getEnfant() === $this) {
                $accueil->setEnfant(null);
            }
        }

        return $this;
    }
}
