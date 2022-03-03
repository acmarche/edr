<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Presence\Accueil;
use Doctrine\Common\Collections\Collection;

trait AccueilsTraits
{
    /**
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
            $accueil->setTuteur($this);
        }

        return $this;
    }

    public function removeAccueil(Accueil $accueil): self
    {
        if ($this->accueils->contains($accueil)) {
            $this->accueils->removeElement($accueil);
            // set the owning side to null (unless already changed)
            if ($accueil->getTuteur() === $this) {
                $accueil->setTuteur(null);
            }
        }

        return $this;
    }
}
