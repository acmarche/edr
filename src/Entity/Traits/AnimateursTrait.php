<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Animateur;
use Doctrine\Common\Collections\Collection;

trait AnimateursTrait
{
    /**
     * @var Animateur[]|Collection
     */
    private Collection|array $animateurs = [];

    /**
     * @return Collection|Animateur[]
     */
    public function getAnimateurs(): Collection
    {
        return $this->animateurs;
    }

    public function addAnimateur(Animateur $animateur): self
    {
        if (!$this->animateurs->contains($animateur)) {
            $this->animateurs[] = $animateur;
            $animateur->addJour($this);
        }

        return $this;
    }

    public function removeAnimateur(Animateur $animateur): self
    {
        if ($this->animateurs->contains($animateur)) {
            $this->animateurs->removeElement($animateur);
            $animateur->removeJour($this);
        }

        return $this;
    }
}
