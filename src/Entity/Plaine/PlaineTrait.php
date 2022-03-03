<?php

namespace AcMarche\Edr\Entity\Plaine;

trait PlaineTrait
{
    private ?Plaine $plaine = null;

    public function getPlaine(): ?Plaine
    {
        return $this->plaine;
    }

    public function setPlaine(?Plaine $plaine): void
    {
        $this->plaine = $plaine;
    }
}
