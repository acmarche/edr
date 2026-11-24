<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Scolaire\Ecole;

trait EcoleTrait
{
    private ?Ecole $ecole = null;

    public function getEcole(): ?Ecole
    {
        return $this->ecole;
    }

    public function setEcole(?Ecole $ecole): void
    {
        $this->ecole = $ecole;
    }
}
