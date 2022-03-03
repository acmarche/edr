<?php

namespace AcMarche\Edr\Entity\Security\Traits;

use AcMarche\Edr\Entity\Animateur;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait AnimateursTrait
{
    /**
     * @var Animateur[]|Collection
     */
    #[ORM\ManyToMany(targetEntity: Animateur::class, inversedBy: 'users')]
    private Collection $animateurs;

    public function getAnimateur(): ?Animateur
    {
        $animateurs = $this->animateurs;
        if (\count($animateurs) > 0) {
            return $animateurs[0];
        }

        return null;
    }

    /**
     * @return Collection|Animateur[]
     */
    public function getAnimateurs(): Collection
    {
        return $this->animateurs;
    }

    public function addAnimateur(Animateur $animateur): self
    {
        if (! $this->animateurs->contains($animateur)) {
            $this->animateurs[] = $animateur;
        }

        return $this;
    }

    public function removeAnimateur(Animateur $animateur): self
    {
        if ($this->animateurs->contains($animateur)) {
            $this->animateurs->removeElement($animateur);
        }

        return $this;
    }
}
