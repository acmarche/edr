<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Jour;

trait JourTrait
{
    private ?Jour $jour = null;

    public function getJour(): Jour
    {
        return $this->jour;
    }

    public function setJour(Jour $jour): void
    {
        $this->jour = $jour;
    }
}
