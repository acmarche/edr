<?php

namespace AcMarche\Edr\Entity\Plaine;

use Doctrine\Common\Collections\Collection;

trait PlaineGroupesTrait
{
    /**
     * @var PlaineGroupe[]|Collection
     */
    private Collection $plaine_groupes;

    /**
     * @return Collection|PlaineGroupe[]
     */
    public function getPlaineGroupes(): Collection
    {
        return $this->plaine_groupes;
    }

    public function addPlaineGroupe(PlaineGroupe $plaineGroupe): self
    {
        if (!$this->plaine_groupes->contains($plaineGroupe)) {
            $this->plaine_groupes[] = $plaineGroupe;
            $plaineGroupe->setPlaine($this);
        }

        return $this;
    }

    public function removePlaineGroupe(PlaineGroupe $plaineGroupe): self
    {
        if ($this->plaine_groupes->contains($plaineGroupe)) {
            $this->plaine_groupes->removeElement($plaineGroupe);
            // set the owning side to null (unless already changed)
            if ($plaineGroupe->getPlaine() === $this) {
                $plaineGroupe->setPlaine(null);
            }
        }

        return $this;
    }
}
