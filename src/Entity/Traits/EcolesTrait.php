<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Scolaire\Ecole;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait EcolesTrait
{
    /**
     * @var Ecole[]|Collection
     */
    #[ORM\ManyToMany(targetEntity: Ecole::class, inversedBy: 'users')]
    private Collection $ecoles;

    /**
     * @return Collection|Ecole[]
     */
    public function getEcoles(): Collection
    {
        return $this->ecoles;
    }

    public function addEcole(Ecole $ecole): self
    {
        if (! $this->ecoles->contains($ecole)) {
            $this->ecoles[] = $ecole;
        }

        return $this;
    }

    public function removeEcole(Ecole $ecole): self
    {
        if ($this->ecoles->contains($ecole)) {
            $this->ecoles->removeElement($ecole);
        }

        return $this;
    }
}
